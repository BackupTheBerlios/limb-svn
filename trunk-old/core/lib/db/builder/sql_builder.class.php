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

define('DB_USE_DEFAULT_CONNECTION', null);

require_once(LIMB_DIR . 'core/lib/db/db_table_factory.class.php');

class sql_builder
{
	/**
	* Method to perform deletes based on values and keys in a
	* criteria.
	* 
	* @param criteria $criteria The criteria to use.
	* @return number of rows affected on success, db_factory_exception on error
	* @access public 
	* @static 
	*/
	function do_delete($criteria, &$connection)
	{		
		if($connection === DB_USE_DEFAULT_CONNECTION)
			$connection =& db_factory::get_connection();
		
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
			$t = db_table_factory :: instance($table_name);

			foreach(array_keys($t->get_columns()) as $column)
			{
				$key = $table_name . '.' . $column;
				if ($criteria->contains_key($key))
				{
					$sb = '';
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
			if ($criteria->is_single_record())
			{
				$sql = "SELECT COUNT(*) FROM " . $table_name . " WHERE " . implode(" AND ", $where_clause);
				$stmt = $connection->prepare_statement($sql);

				if (is_error($e = sql_builder::populate_stmt_values($stmt, $select_params)))
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
					return new exception(DB_ERROR, "Expecting to delete 1 record, but criteria match multiple.");
				} 
				$rs->close();
			} 

			$sql = "DELETE FROM " . $table_name
			. ($where_clause ? " WHERE " . implode(" AND ", $where_clause) : '');
			
			$stmt = &$connection->prepare_statement($sql);

			if (is_error($e = sql_builder::populate_stmt_values($stmt, $select_params)))
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
	* If no primary key is defined for the table the values will be
	* inserted as specified in criteria and null will be returned.
	* 
	* @param criteria $criteria Object containing values to insert.
	* @param connection $connection A connection.
	* @return mixed An Object which is the id of the row that was inserted
	* (if the table has a primary key) or null (if the table does not
	* have a primary key) OR db_factory_exception on error.
	*/
	function do_insert($criteria, &$connection)
	{
		if($connection === DB_USE_DEFAULT_CONNECTION)
			$connection =& db_factory::get_connection();

		// the primary key
		$id = null; 
		// Get the table name and method for determining the primary
		// key value.
		$keys = $criteria->keys();

		if (empty($keys))
		{
			return new exception(DB_ERROR, "Database insert attempted without anything specified to insert");
		} 

		$table_name = $criteria->get_table_name($keys[0]);

		$table =& db_table_factory :: instance($table_name);
		$key_name = &$table->get_primary_key_name();
		$use_id_gen = $table->use_id_generator();
		$key_gen = &$connection->get_id_generator();

		// only get a new key value if you need to
		// the reason is that a primary key might be defined
		// but you are still going to set its value. for example:
		// a join table where both keys are primary and you are
		// setting both columns with your own values
		// pk will be null if there is no primary key defined for the table
		// we're inserting into.
		if ($key_name !== null && ! $criteria->contains_key($key_name))
		{ 
			// If the key_method is SEQUENCE get the id before the insert.
			if ($key_gen->is_before_insert())
			{
				$id = $key_gen->get_id($key_name);
				if (is_error($id))
				{
					return new exception(DB_ERROR, "Unable to get sequence id.", $id);
				} 
				$criteria->add($key_name, $id);
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

		$stmt =& $connection->prepare_statement($sql);
		$params =& sql_builder::build_params($qualified_cols, $criteria);

		if (is_error($e = sql_builder::populate_stmt_values($stmt, $params)))
		{
			return new exception("Unable to execute INSERT statement.", $e);
		} 

		if (is_error($e = $stmt->execute_update()))
		{
			return new exception(DB_ERROR, "Unable to execute INSERT statement.", $e);
		} 
		// If the primary key column is auto-incremented, get the id
		// now.
		if ($key_name !== null && $use_id_gen && $key_gen->is_after_insert())
		{
			$id = $key_gen->get_id($key_name);
			if (is_error($id))
			{
				return new exception(DB_ERROR, "Unable to get autoincrement id.", $id);
			} 
		} 

		return $id;
	} 
	
	/**
	* Method used to update rows in the DB.  Rows are selected based
	* on selectcriteria and updated using values in update_values.
	* <p>
	* Use this method for performing an update of the kind:
	* <p>
	* WHERE some_column = some value AND could_have_another_column =
	* another value AND so on.
	* 
	* @param  $selectcriteria A criteria object containing values used in where
	*         clause.
	* @param  $update_values A criteria object containing values used in set
	*         clause.
	* @param  $connection A connection.
	* @return db_factory_exception on error
	* @static public
	*/
	function do_update(&$select_criteria, &$update_values, &$connection)
	{
		if($connection === DB_USE_DEFAULT_CONNECTION)
			$connection =& db_factory::get_connection();

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
				$c =& $select_criteria->get_criterion($col_name);

				if (is_error($e = $c->append_ps_to($sb, $select_params)))
					return $e;

				$where_clause[] = $sb;
			} 

			$rs = null;
			$stmt = null;

			$sql_snippet = implode(" AND ", $where_clause);

			if ($select_criteria->is_single_record())
			{ 
				// Get affected records.
				$sql = "SELECT COUNT(*) FROM " . $table_name . " WHERE " . $sql_snippet;
				$stmt =& $connection->prepare_statement($sql);

				if (is_error($e = sql_builder::populate_stmt_values($stmt, $select_params)))
					return $e;

				$rs =& $stmt->execute_query(result_set::FETCHMODE_NUM());
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
						return new exception(DB_ERROR, "Expected to update 1 record, multiple matched.");
					} 
					$rs->close();
				} 
			} 

			$sql = "UPDATE " . $table_name . " SET ";
			foreach($update_tables_columns[$table_name] as $col)
			{
				$sql .= substr($col, strpos($col, '.') + 1) . '=?,';
			} 

			$sql = substr($sql, 0, -1) . " WHERE " . $sql_snippet;

			$stmt =& $connection->prepare_statement($sql); 
			// Replace '?' with the actual values
			$params =& sql_builder::build_params($update_tables_columns[$table_name], $update_values);

			if (is_error($e = sql_builder::populate_stmt_values($stmt, array_merge($params, $select_params))))
				return $e;

			if (is_error($e = $stmt->execute_update()))
			{
				if ($rs) 
					$rs->close();
				if ($stmt) 
					$stmt->close();
					
				return new exception(DB_ERROR, "Unable to execute UPDATE statement.", $e);
			} 

			$stmt->close();
		} // foreach table in the criteria
		return true;
	} 

	/**
	* Executes query build by create_select_sql() and returns result_set.
	* 
	* @param criteria $criteria A criteria.
	* @param connection $connection A connection to use.
	* @return result_set The resultset or db_factory_exception on error.
	* @see create_select_sql
	* @protected 
	* @static 
	*/
	function &do_select(&$criteria, &$connection)
	{
		$stmt = null;
		
		if($connection === DB_USE_DEFAULT_CONNECTION)
			$connection =& db_factory::get_connection();

		$params = array();
		$sql = sql_builder :: create_select_sql($criteria, $params, $connection);
		
		if (is_error($sql))
		{
			return $sql;
		} 

		$stmt = &$connection->prepare_statement($sql);
		$stmt->set_limit($criteria->get_limit());
		$stmt->set_offset($criteria->get_offset());

		if (is_error($e = sql_builder::populate_stmt_values($stmt, $params)))
			return $e;

		$rs = &$stmt->execute_query(result_set::FETCHMODE_ASSOC());
		if (is_error($rs))
		{
			return new exception(DB_ERROR, "Unable to execute SELECT statement !", $rs);
		} 

		return $rs;
	} 

	/**
	* Helper method which returns the primary key contained
	* in the given criteria object.
	* 
	* @param criteria $criteria A criteria.
	* @return column_map If the criteria object contains a primary
	*           key, or null if it doesn't.
	* @throws db_factory_exception
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
			$t =& db_table_factory :: instance($table);
			$pk = $t->get_primary_key_name();
		} 

		return $pk;
	} 

	/**
	* Method to create an SQL query based on values in a criteria.
	* 
	* This method creates only prepared statement SQL (using ? where values
	* will go).  The second parameter ($params) stores the values that need
	* to be set before the statement is executed.  The reason we do it this way
	* is to let the db layer handle all escaping & value formatting.
	* 
	* @param criteria $criteria criteria for the SELECT query.
	* @param array $ &$params Parameters that are to be replaced in prepared statement.
	* @return string 
	* @throws db_factory_exception Trouble creating the query string.
	*/
	function create_select_sql(&$criteria, &$params, &$connection)
	{
		if($connection === DB_USE_DEFAULT_CONNECTION)
			$connection =& db_factory::get_connection();
		
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
			// the from_clause if this function has a TABLE.COLUMN in it at all.
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
		// included via a LEFT JOIN
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

				$t =& db_table_factory :: instance($table);
				$type =& $t->get_column_type($some_criteria[$i]->get_column());

				$ignore_case = (
					($criteria->is_ignore_case() || $some_criteria[$i]->is_ignore_case()) && (!$type)
				);

				$some_criteria[$i]->set_ignore_case($ignore_case);
			} 

			$criterion->set_connection($connection);

			$sb = '';

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

				$t =& db_table_factory :: instance($table);
				$type =& $t->get_column_type(substr($join2, $dot + 1));

				$ignore_case = ($criteria->is_ignore_case() && (!$type)
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
			$sb = '';

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

				$t =& db_table_factory :: instance($table);
				if (!$t->get_column_type($column_name))
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
		 . ($where_clause ? " WHERE " . implode(" AND ", $where_clause) : '')
		 . ($order_by_clause ? " ORDER BY " . implode(",", $order_by_clause) : '')
		 . ($group_by_clause ? " GROUP BY " . implode(",", $group_by_clause) : '')
		 . ($having_string ? " HAVING " . $having_string : '');

		return $sql;
	} 

	/**
	* Builds a params array, like the kind populated by Criterion::append_ps_to().
	* This is useful for building an array even when it is not using the append_ps_to() method.
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
	* @param prepared_statement $stmt 
	* @param array $params array('column' => ..., 'table' => ..., 'value' => ...)
	* @return int The number of params replaced.
	* @private static
	*/
	function populate_stmt_values(&$stmt, &$params)
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
				$t =& db_table_factory :: instance($table_name);
				$affix = db_types :: get_affix($t->get_column_type($column_name));

				if (is_error($affix))
				{
					return new exception(DB_ERROR, $affix);
				} 
				
				$setter = 'set_' . $affix;
				
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
			$instance = new sql_builder();
		} 

		return $instance;
	} 
} 