<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

/**
* This is the base class for all Peer classes in the system.  Peer
* classes are responsible for isolating all of the database access
* for a specific business object.  They execute all of the SQL
* against the database.  Over time this class has grown to include
* utility methods which ease execution of cross-database queries and
* the implementation of concrete peers.
* (inspired by propel project http://propel.phpdbg.org)
*/
class db_peer
{
	/**
	* * Array (hash) that contains the cached mapBuilders.
	*/
	var $map_builders = array();
	var $validator_map = array();

	/**
	* Method to perform deletes based on values and keys in a
	* criteria.
	* 
	* @param criteria $criteria The criteria to use.
	* @return number of rows affected on success, db_factoryException on error
	* @access public 
	* @static 
	*/
	function do_delete($criteria)
	{
		if (! is_a($criteria, 'criteria'))
			return new exception (DB_PEER_ERROR, "parameter 1 not of type 'criteria' !");

		$connection= &db_factory::get_connection();
		$db_map = &db_factory::get_database_map();

		if (is_error($db_map))
		{
			return $db_map;
		} 
		// Set up a list of required tables (one DELETE statement will
		// be executed per table)
		$tables_keys = array();

		for (($it = &$criteria->get_iterator()); $it->valid(); $it->next())
		{
			$c = &$it->current();
			foreach($c->get_all_tables() as $table_name)
			{
				$table_name2 = $criteria->get_table_for_alias($table_name);
				if ($table_name2 !== null)
				{
					$tables_keys[$table_name2 . ' ' . $table_name] = true;
				} 
				else
				{
					$tables_keys[$table_name] = true;
				} 
			} 
		} // foreach criteria->keys()
		$tables = array_keys($tables_keys);

		foreach($tables as $table_name)
		{
			$where_clause = array();
			$select_params = array();
			$t = &$db_map->get_table($table_name);

			foreach($t->get_columns() as $col_map)
			{
				$key = $table_name . '.' . $col_map->get_column_name();
				if ($criteria->contains_key($key))
				{
					$sb = "";
					$c = &$criteria->get_criterion($key);
					$e = $c->append_ps_to($sb, $select_params);
					if (is_error($e))
					{
						return $e;
					} 
					$where_clause[] = $sb;
				} 
			} 
			// Execute the statement.
			$sql_snippet = implode(" AND ", $where_clause);

			if ($criteria->is_single_record())
			{
				$sql = "SELECT COUNT(*) FROM " . $table_name . " WHERE " . $sql_snippet;
				$stmt = $con->prepare_statement($sql);

				if (is_error($e = db_peer::populate_stmt_values($stmt, $select_params, $db_map)))
					return $e;

				$rs = &$stmt->execute_query(result_set::FETCHMODE_NUM());
				if (is_error($rs))
				{
					return new exception(DB_ERROR, "Unable to execute DELETE statement.", $rs);
				} 
				$rs->next();
				if ($rs->get_int(1) > 1)
				{
					$rs->close();
					return new exception(DB_PEER_ERROR, "Expecting to delete 1 record, but criteria match multiple.");
				} 
				$rs->close();
			} 

			$sql = "DELETE FROM " . $table_name . " WHERE " . $sql_snippet;
			$stmt = &$con->prepare_statement($sql);

			if (is_error($e = db_peer::populate_stmt_values($stmt, $select_params, $db_map)))
				return $e;

			$result = $stmt->execute_update();
			if (is_error($result))
			{
				return new exception(DB_ERROR, "Unable to execute DELETE statement.", $result);
			} 
		} // for each table
		return $result;
	} 

	/**
	* Method to perform inserts based on values and keys in a
	* criteria.
	* <p>
	* If the primary key is auto incremented the data in criteria
	* will be inserted and the auto increment value will be returned.
	* <p>
	* If the primary key is included in criteria then that value will
	* be used to insert the row.
	* <p>
	* If no primary key is included in criteria then we will try to
	* figure out the primary key from the database map and insert the
	* row with the next available id using util.db.IDBroker.
	* <p>
	* If no primary key is defined for the table the values will be
	* inserted as specified in criteria and null will be returned.
	* 
	* @param criteria $criteria Object containing values to insert.
	* @param connection $con A connection.
	* @return mixed An Object which is the id of the row that was inserted
	* (if the table has a primary key) or null (if the table does not
	* have a primary key) OR db_factoryException on error.
	*/
	function do_insert($criteria, &$con)
	{
		// the primary key
		$id = null; 
		// Get the table name and method for determining the primary
		// key value.
		$keys = $criteria->keys();

		if (empty($keys))
		{
			return new exception(DB_PEER_ERROR, "Database insert attempted without anything specified to insert");
		} 

		$table_name = $criteria->get_table_name($keys[0]);

		$db_map = &db_factory::get_database_map();

		if (is_error($db_map))
			return $db_map;

		$table_map = &$db_map->get_table($table_name);
		$key_info = &$table_map->get_primary_key_method_info();
		$use_id_gen = $table_map->is_use_id_generator();
		$key_gen = &$con->get_id_generator();

		$pk = db_peer::get_primary_key($criteria); 
		// only get a new key value if you need to
		// the reason is that a primary key might be defined
		// but you are still going to set its value. for example:
		// a join table where both keys are primary and you are
		// setting both columns with your own values
		// pk will be null if there is no primary key defined for the table
		// we're inserting into.
		if ($pk !== null && ! $criteria->contains_key($pk->get_fully_qualified_name()))
		{ 
			// If the keyMethod is SEQUENCE get the id before the insert.
			if ($key_gen->is_before_insert())
			{
				$id = $key_gen->get_id($key_info);
				if (is_error($id))
				{
					return new exception(DB_PEER_ERROR, "Unable to get sequence id.", $id);
				} 
				$criteria->add($pk->get_fully_qualified_name(), $id);
			} 
		} 

		$qualified_cols = $criteria->keys(); // we need table.column cols when populating values
		$columns = array(); // but just 'column' cols for the SQL
		
		foreach($qualified_cols as $qualified_col)
		{
			$columns[] = substr($qualified_col, strpos($qualified_col, '.') + 1);
		} 

		$sql = "INSERT INTO " . $table_name
		 . " (" . implode(",", $columns) . ")"
		 . " VALUES (" . substr(str_repeat("?,", count($columns)), 0, -1) . ")";

		$stmt = &$con->prepare_statement($sql);
		$params = &db_peer::build_params($qualified_cols, $criteria);

		if (is_error($e = db_peer::populate_stmt_values($stmt, $params, $db_map)))
		{
			return new exception("Unable to execute INSERT statement.", $e);
		} 

		if (is_error($e = $stmt->execute_update()))
		{
			return new exception(DB_ERROR, "Unable to execute INSERT statement.", $e);
		} 
		// If the primary key column is auto-incremented, get the id
		// now.
		if ($pk !== null && $use_id_gen && $key_gen->is_after_insert())
		{
			$id = $key_gen->get_id($key_info);
			if (is_error($id))
			{
				return new exception(DB_ERROR, "Unable to get autoincrement id.", $id);
			} 
		} 

		return $id;
	} 

	/**
	* Method used to update rows in the DB.  Rows are selected based
	* on selectcriteria and updated using values in updateValues.
	* <p>
	* Use this method for performing an update of the kind:
	* <p>
	* WHERE some_column = some value AND could_have_another_column =
	* another value AND so on.
	* 
	* @param  $selectcriteria A criteria object containing values used in where
	*         clause.
	* @param  $updateValues A criteria object containing values used in set
	*         clause.
	* @param  $con A connection.
	* @return db_factoryException on error
	* @static public
	*/
	function do_update(&$select_criteria, &$update_values, &$con)
	{
		$connection= &db_factory::get_connection();
		$db_map = &db_factory::get_database_map();

		if (is_error($db_map))
			return $db_map; 
		// Get list of required tables, containing all columns
		$tables_columns = $select_criteria->get_tables_columns(); 
		// we also need the columns for the update SQL
		$update_tables_columns = $update_values->get_tables_columns();

		foreach($tables_columns as $table_name => $columns)
		{
			$where_clause = array();
			$select_params = array();

			foreach($columns as $col_name)
			{
				$sb = "";
				$c = &$select_criteria->get_criterion($col_name);

				if (is_error($e = $c->append_ps_to($sb, $select_params)))
					return $e;

				$where_clause[] = &$sb;
			} 

			$rs = null;
			$stmt = null;

			$sql_snippet = implode(" AND ", $where_clause);

			if ($select_criteria->is_single_record())
			{ 
				// Get affected records.
				$sql = "SELECT COUNT(*) FROM " . $table_name . " WHERE " . $sql_snippet;
				$stmt = &$con->prepare_statement($sql);

				if (is_error($e = db_peer::populate_stmt_values($stmt, $select_params, $db_map)))
					return $e;

				$rs = &$stmt->execute_query(result_set::FETCHMODE_NUM());
				if (is_error($rs))
				{
					return new exception(DB_ERROR, "Unable to execute UPDATE statement !", $rs);
				} 
				if ($rs)
				{
					$rs->next();
					if ($rs->get_int(1) > 1)
					{
						$rs->close();
						return new exception(DB_PEER_ERROR, "Expected to update 1 record, multiple matched.");
					} 
					$rs->close();
				} 
			} 

			$sql = "UPDATE " . $table_name . " SET ";
			foreach($update_tables_columns[$table_name] as $col)
			{
				$sql .= substr($col, strpos($col, '.') + 1) . " = ?,";
			} 

			$sql = substr($sql, 0, -1) . " WHERE " . $sql_snippet;

			$stmt = &$con->prepare_statement($sql); 
			// Replace '?' with the actual values
			$params = &db_peer::build_params($update_tables_columns[$table_name], $update_values);

			if (is_error($e = db_peer::populate_stmt_values($stmt, array_merge($params, $select_params), $db_map)))
				return $e;

			if (is_error($e = $stmt->execute_update()))
			{
				if ($rs) $rs->close();
				if ($stmt) $stmt->close();
				return new exception(DB_ERROR, "Unable to execute UPDATE statement.", $e);
			} 

			$stmt->close();
		} // foreach table in the criteria
		return true;
	} 

	/**
	* Executes query build by createSelectSql() and returns ResultSet.
	* 
	* @param criteria $criteria A criteria.
	* @param connection $con A connection to use.
	* @return ResultSet The resultset or db_factoryException on error.
	* @see createSelectSql
	* @protected 
	* @static 
	*/
	function &do_select(&$criteria, &$con)
	{
		$db_map = db_factory::get_database_map();
		$stmt = null;

		if (is_error($db_map))
			return $db_map;

		if ($con->get_auto_commit() === true)
		{ 
			// transaction support exists for (only?) Postgres, which must
			// have SELECT statements that include bytea columns wrapped w/
			// transactions.
			$con =& transaction::begin_optional($criteria->get_connection_name(), $criteria->is_use_transaction());
			if (is_error($con))
			{
				return $con;
			} 
		} 

		$params = array();
		$sql = db_peer::create_select_sql($criteria, $params);
		if (is_error($sql))
		{
			return $sql;
		} 

		$stmt = &$con->prepare_statement($sql);
		$stmt->set_limit($criteria->get_limit());
		$stmt->set_offset($criteria->get_offset());

		if (is_error($e = db_peer::populate_stmt_values($stmt, $params, $db_map)))
			return $e;

		$rs = &$stmt->execute_query(result_set::FETCHMODE_NUM());
		if (is_error($rs))
		{
			return new exception(DB_ERROR, "Unable to execute SELECT statement !", $rs);
		} 

		if (is_error($e = transaction::commit($con)))
		{
			if ($stmt) $stmt->close();
			if (is_error($e2 = transaction::rollback($con)))
			{
				return $e2;
			} 
			return $e;
		} 

		return $rs;
	} 

	/**
	* Helper method which returns the primary key contained
	* in the given criteria object.
	* 
	* @param criteria $criteria A criteria.
	* @return ColumnMap If the criteria object contains a primary
	*           key, or null if it doesn't.
	* @throws db_factoryException
	* @private static
	*/
	function get_primary_key($criteria)
	{ 
		// Assume all the keys are for the same table.
		$keys = $criteria->keys();
		$key = $keys[0];
		$table = $criteria->get_table_name($key);

		$pk = null;

		if (!empty($table))
		{
			$db_map = db_factory::get_database_map();

			if (is_error($db_map))
				return $db_map;

			if ($db_map->get_table($table) == null)
				return new exception(DB_PEER_ERROR, "\$db_map->get_table() is null");

			$t = &$db_map->get_table($table);
			$columns = $t->get_columns();
			foreach(array_keys($columns) as $key)
			{
				if ($columns[$key]->is_primary_key())
				{
					$pk = $columns[$key];
					break;
				} 
			} 
		} 

		return $pk;
	} 

	/**
	* Method to create an SQL query based on values in a criteria.
	* 
	* This method creates only prepared statement SQL (using ? where values
	* will go).  The second parameter ($params) stores the values that need
	* to be set before the statement is executed.  The reason we do it this way
	* is to let the Creole layer handle all escaping & value formatting.
	* 
	* @param criteria $criteria criteria for the SELECT query.
	* @param array $ &$params Parameters that are to be replaced in prepared statement.
	* @return string 
	* @throws db_factoryException Trouble creating the query string.
	*/
	function create_select_sql(&$criteria, &$params)
	{
		$connection = &db_factory::get_connection();
		$db_map = &db_factory::get_database_map();

		if (is_error($db_map))
			return $db_map; 
		// redundant definition $select_modifiers = array();
		$select_clause = array();
		$from_clause = array();
		$where_clause = array();
		$order_by_clause = array(); 
		// redundant definition $group_by_clause = array();
		$order_by = $criteria->get_order_by_columns();
		$group_by = $criteria->get_group_by_columns();
		$ignore_case = $criteria->is_ignore_case();
		$select = $criteria->get_select_columns();
		$aliases = $criteria->get_as_columns(); 
		// simple copy
		$select_modifiers = $criteria->get_select_modifiers(); 
		// get selected columns
		foreach($select as $column_name)
		{ 
			// expect every column to be of "table.column" formation
			// it could be a function:  e.g. MAX(books.price)
			$table_name = null;
			$select_clause[] = $column_name; // the full column name: e.g. MAX(books.price)
			$paren_pos = strpos($column_name, '(');
			$dot_pos = strpos($column_name, '.'); 
			// [HL] I think we really only want to worry about adding stuff to
			// the fromClause if this function has a TABLE.COLUMN in it at all.
			// e.g. COUNT(*) should not need this treatment -- or there needs to
			// be special treatment for '*'
			if ($dot_pos !== false)
			{
				if ($paren_pos === false) // table.column
				{
					$table_name = substr($column_name, 0, $dot_pos);
				} 
				else // FUNC(table.column)
				{
					$table_name = substr($column_name, $paren_pos + 1, $dot_pos - ($paren_pos + 1)); 
					$last_space = strpos($table_name, ' ');
					if ($last_space !== false) // COUNT(DISTINCT books.price)
					{
						$table_name = substr($table_name, $last_space + 1);
					} 
				} 

				$table_name2 = $criteria->get_table_for_alias($table_name);
				if ($table_name2 !== null)
				{
					$from_clause[] = $table_name2 . ' ' . $table_name;
				} 
				else
				{
					$from_clause[] = $table_name;
				} 
			}
		}
		// set the aliases
		foreach($aliases as $alias => $col)
		{
			$select_clause[] = $col . " AS " . $alias;
		} 
		// add the criteria to WHERE clause
		// this will also add the table names to the FROM clause if they are not already
		// invluded via a LEFT JOIN
		foreach($criteria->keys() as $key)
		{
			$criterion = &$criteria->get_criterion($key);
			$some_criteria = &$criterion->get_attached_criterion();
			$some_criteria_length = count($some_criteria);
			$table = null;

			for ($i = 0; $i < $some_criteria_length; $i++)
			{
				$table_name = $some_criteria[$i]->get_table();

				$table = $criteria->get_table_for_alias($table_name);
				if ($table !== null)
				{
					$from_clause[] = $table . ' ' . $table_name;
				} 
				else
				{
					$from_clause[] = $table_name;
					$table = $table_name;
				} 

				$t = &$db_map->get_table($table);
				$col = &$t->get_column($some_criteria[$i]->get_column());

				$type = $col->get_type();

				$ignore_case = (
					($criteria->is_ignore_case() || $some_criteria[$i]->is_ignore_case()) && ($type == "string")
				);

				$some_criteria[$i]->set_ignore_case($ignore_case);
			} 

			$criterion->set_connection($connection);

			$sb = "";

			if (is_error($e = $criterion->append_ps_to($sb, $params)))
				return $e;

			$where_clause[] = $sb;
		} 
		// handle RIGHT (straight) joins
		// This adds tables to the FROM clause and adds WHERE clauses.  Not sure if this shouldn't
		// be changed to use INNER JOIN
		$join = $criteria->get_join_l();
		if ($join !== null)
		{
			$join_r = $criteria->get_join_r();
			for ($i = 0, $join_size = count($join); $i < $join_size; $i++)
			{
				$join1 = (string) $join[$i];
				$join2 = (string) $join_r[$i];

				$table_name = substr($join1, 0, strpos($join1, '.'));
				$table = $criteria->get_table_for_alias($table_name);
				if ($table != null)
				{
					$from_clause[] = $table . ' ' . $table_name;
				} 
				else
				{
					$from_clause[] = $table_name;
				} 

				$dot = strpos($join2, '.');
				$table_name = substr($join2, 0, $dot);
				$table = $criteria->get_table_for_alias($table_name);
				if ($table !== null)
				{
					$from_clause[] = $table . ' ' . $table_name;
				} 
				else
				{
					$from_clause[] = $table_name;
					$table = $table_name;
				} 

				$t = &$db_map->get_table($table);
				$col = &$t->get_column(substr($join2, $dot + 1));
				$type = &$col->get_type();

				$ignore_case = ($criteria->is_ignore_case() && ($type == "string")
					);
				if ($ignore_case)
				{
					$where_clause[] = $connection->ignore_case($join1) . '=' . $connection->ignore_case($join2);
				} 
				else
				{
					$where_clause[] = $join1 . '=' . $join2;
				} 
			} 
		} 
		// Unique from clause elements
		$from_clause = array_unique($from_clause); 
		// Add the GROUP BY columns
		$group_by_clause = $group_by;

		$having = $criteria->get_having();
		$having_string = null;

		if ($having !== null)
		{
			$sb = "";

			if (is_error($e = $having->append_ps_to($sb, $params)))
				return $e;

			$having_string = $sb;
		} 

		if (!empty($order_by))
		{
			foreach($order_by as $order_by_column)
			{
				$dot_pos = strpos($order_by_column, '.');
				$table = substr($order_by_column, 0, $dot_pos); 
				// See if there's a space (between the column list and sort
				// order in ORDER BY table.column DESC).
				$space_pos = strpos($order_by_column, ' ');

				if ($space_pos === false)
				{
					$column_name = substr($order_by_column, $dot_pos + 1);
				} 
				else
				{
					$column_name = substr($order_by_column, $dot_pos + 1, $space_pos - ($dot_pos + 1));
				} 

				$t = &$db_map->get_table($table);
				$column = $t->get_column($column_name);
				if ($column->get_type() == "string")
				{
					if ($space_pos === false)
					{
						$order_by_clause[] = $connection->ignore_case_in_order_by($order_by_column);
					} 
					else
					{
						$order_by_clause[] = $connection->ignore_case_in_order_by(substr($order_by_column, 0, $space_pos)) . substr($order_by_column, $space_pos);
					} 
					$select_clause[] = $connection->ignore_case_in_order_by($table . '.' . $column_name);
				} 
				else
				{
					$order_by_clause[] = $order_by_column;
				} 
			} 
		} 
		// Build the SQL from the arrays we compiled
		$sql = "SELECT " . implode(", ", $select_clause)
		 . " FROM " . implode(", ", $from_clause)
		 . ($where_clause ? " WHERE " . implode(" AND ", $where_clause) : "")
		 . ($order_by_clause ? " ORDER BY " . implode(",", $order_by_clause) : "")
		 . ($group_by_clause ? " GROUP BY " . implode(",", $group_by_clause) : "")
		 . ($having_string ? " HAVING " . $having_string : "");

		return $sql;
	} 

	/**
	* Builds a params array, like the kind populated by Criterion::appendPsTo().
	* This is useful for building an array even when it is not using the appendPsTo() method.
	* 
	* @param array $columns 
	* @param criteria $values 
	* @return array params array('column' => ..., 'table' => ..., 'value' => ...)
	* @private static
	*/
	function build_params($columns, $values)
	{
		$params = array();
		foreach($columns as $key)
		{
			if ($values->contains_key($key))
			{
				$crit = $values->get_criterion($key);
				$params[] = array('column' => $crit->get_column(), 'table' => $crit->get_table(), 'value' => $crit->get_value());
			} 
		} 
		return $params;
	} 

	/**
	* Populates values in a prepared statement.
	* 
	* @param PreparedStatement $stmt 
	* @param array $params array('column' => ..., 'table' => ..., 'value' => ...)
	* @param DatabaseMap $connectionap 
	* @return int The number of params replaced.
	* @private static
	*/
	function populate_stmt_values(&$stmt, &$params, &$db_map)
	{
		$i = 1;
		foreach($params as $param)
		{
			$table_name = $param['table'];
			$column_name = $param['column'];
			$value = $param['value'];

			if ($value === null)
			{
				$stmt->set_null($i++);
			} 
			else
			{
				$t = &$db_map->get_table($table_name);
				$c_map = $t->get_column($column_name);
				$setter = 'set' . db_types::get_affix($c_map->get_db_type());

				if (is_error($setter))
				{
					return new exception(DB_ERROR, $setter);
				} 

				$stmt->$setter($i++, $value);
			} 
		}
	} 

	/*
  * @private
  */
	function &instance()
	{
		static $instance;

		if ($instance === null)
		{
			$instance = new db_peer();
		} 

		return $instance;
	} 
} 