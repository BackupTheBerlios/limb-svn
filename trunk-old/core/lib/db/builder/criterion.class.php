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
define('CRITERION_AND', " AND ");
define('CRITERION_OR', " OR ");

/**
* This is an "inner" class that describes an object in the criteria.
* (inspired by propel project http://propel.phpdbg.org)
*/
class criterion
{
	/**
	* * Value of the CO.
	*/
	var $value;

	/**
	* * Comparison value.
	* 
	* @var sql_enum 
	*/
	var $comparison;

	/**
	* * Table name.
	*/
	var $table;

	/**
	* * Column name.
	*/
	var $column;

	/**
	* * flag to ignore case in comparision
	*/
	var $ignore_string_case = false;

	/**
	* The DBAdapter adaptor which might be used to get db specific
	* variations of sql.
	*/
	var $connection;

	/**
	* other connected criteria and their conjunctions.
	*/
	var $clauses = array();
	var $conjunctions = array();

	/**
	* * "Parent" criteria class
	*/
	var $parent;

	/**
	* Create a new instance.
	* 
	* @param criteria $parent The outer class (this is an "inner" class).
	* @param string $column TABLE.COLUMN format.
	* @param mixed $value 
	* @param string $comparison 
	*/
	function criterion(&$outer, $column, $value, $comparison = null)
	{
		$this->outer = &$outer;
		list($this->table, $this->column) = explode('.', $column);
		$this->value = $value;
		$this->comparison = ($comparison === null ? criteria::EQUAL() : $comparison);
	} 

	/**
	* Get the column name.
	* 
	* @return string A String with the column name.
	*/
	function &get_column()
	{
		return $this->column;
	} 

	/**
	* Set the table name.
	* 
	* @param name $ A String with the table name.
	* @return void 
	*/
	function set_table($name)
	{
		$this->table = $name;
	} 

	/**
	* Get the table name.
	* 
	* @return string A String with the table name.
	*/
	function &get_table()
	{
		return $this->table;
	} 

	/**
	* Get the comparison.
	* 
	* @return string A String with the comparison.
	*/
	function &get_comparison()
	{
		return $this->comparison;
	} 

	/**
	* Get the value.
	* 
	* @return mixed An Object with the value.
	*/
	function get_value()
	{
		return $this->value;
	} 

	/**
	* Get the value of db.
	* The DBAdapter which might be used to get db specific
	* variations of sql.
	* 
	* @return DBAdapter value of db.
	*/
	function &get_connection()
	{
		$connection = null;
		if ($this->connection === null)
		{ 
			$connection =& db_factory :: get_connection($this->outer->get_connection_name());
		} 
		else
		{
			$connection =& $this->connection;
		} 

		return $connection;
	} 

	/**
	* Set the value of db.
	* The DBAdapter adaptor might be used to get db specific
	* variations of sql.
	* 
	* @param v $ Value to assign to db.
	* @return void 
	*/
	function set_connection(&$v)
	{
		$this->connection = &$v;
		for($i = 0, $_i = count($this->clauses); $i < $_i; $i++)
		{
			$this->clauses[$i]->set_connection($v);
		} 
	} 

	/**
	* Sets ignore case.
	* 
	* @param boolean $b True if case should be ignored.
	* @return criterion A modified criterion object.
	*/
	function set_ignore_case($b)
	{
		$this->ignore_string_case = $b;
		return $this;
	} 

	/**
	* Is ignore case on or off?
	* 
	* @return boolean True if case is ignored.
	*/
	function is_ignore_case()
	{
		return $this->ignore_string_case;
	} 

	/**
	* Get the list of clauses in this criterion.
	* 
	* @return array 
	* @private 
	*/
	function &get_clauses()
	{
		return $this->clauses;
	} 

	/**
	* Get the list of conjunctions in this criterion
	* 
	* @return array 
	* @private 
	*/
	function &get_conjunctions()
	{
		return $this->conjunctions;
	} 

	/**
	* Append an AND criterion onto this criterion's list.
	*/
	function &add_and($criterion)
	{
		$this->clauses[] = &$criterion;
		$this->conjunctions[] = CRITERION_AND;
		return $this;
	} 

	/**
	* Append an OR criterion onto this criterion's list.
	* 
	* @return criterion 
	*/
	function &add_or($criterion)
	{
		$this->clauses[] = &$criterion;
		$this->conjunctions[] = CRITERION_OR;
		return $this;
	} 

	/**
	* Appends a Prepared Statement representation of the criterion
	* onto the buffer.
	* 
	* @param string $ &$sb The stringbuffer that will receive the Prepared Statement
	* @param array $params A list to which Prepared Statement parameters
	*                       will be appended
	* @return propel_exception If an error occures.
	*/
	function append_ps_to(&$sb, &$params)
	{
		if ($this->column === null)
		{
			return;
		} 

		$connection= &$this->get_connection();
		$clauses_length = count($this->clauses);

		for($j = 0; $j < $clauses_length; $j++)
		{
			$sb .= '(';
		} 

		if (criteria::CUSTOM() === $this->comparison)
		{
			if ($this->value !== '')
			{
				$sb .= (string)$this->value;
			} 
		} 
		else
		{
			if ($this->table === null)
			{
				$field = $this->column;
			} 
			else
			{
				$field = $this->table . '.' . $this->column;
			} 
			// There are several different types of expressions that need individual handling:
			// IN/NOT IN, LIKE/NOT LIKE, and traditional expressions.
			// OPTION 1:  table.column IN (?, ?) or table.column NOT IN (?, ?)
			if ($this->comparison === criteria::IN() || $this->comparison === criteria::NOT_IN())
			{
				$sb .= $field . $this->comparison;
				$values = (array) $this->value;
				for ($i = 0, $values_length = count($values); $i < $values_length; $i++)
				{
					$params[] = array('table' => $this->table, 'column' => $this->column, 'value' => $values[$i]);
				} 
				$in_string = '(' . substr(str_repeat("?,", $values_length), 0, -1) . ')';
				$sb .= $in_string;
			} 
			// OPTION 2:  table.column LIKE ? or table.column NOT LIKE ?  (or ILIKE for Postgres)
			elseif ($this->comparison === criteria::LIKE() || $this->comparison === criteria::NOT_LIKE() || $this->comparison === criteria::ILIKE() || $this->comparison === criteria::NOT_ILIKE())
			{ 
				// Handle LIKE, NOT LIKE (and related ILIKE, NOT ILIKE for Postgres)
				// If selection is case insensitive use ILIKE for postgre_sql or SQL
				// UPPER() function on column name for other databases.
				if ($this->ignore_string_case)
				{
					if (is_a($connection, 'db_postgres'))
					{
						if ($this->comparison === criteria::LIKE())
						{
							$this->comparison = criteria::ILIKE();
						} elseif ($comparison === criteria::NOT_LIKE())
						{
							$this->comparison = criteria::NOT_ILIKE();
						} 
					} 
					else
					{
						$field = $connection->ignore_case($field);
					} 
				} 

				$sb .= $field . $this->comparison; 
				// If selection is case insensitive use SQL UPPER() function
				// on criteria or, if Postgres we are using ILIKE, so not necessary.
				if ($this->ignore_string_case && !is_a($connection, 'db_postgres'))
				{
					$sb .= $connection->ignore_case('?');
				} 
				else
				{
					$sb .= '?';
				} 

				$params[] = array('table' => $this->table, 'column' => $this->column, 'value' => $this->value);
			} 
			// OPTION 3:  table.column = ? or table.column >= ? etc. (traditional expressions, the default)
			else
			{ 
				// NULL VALUES need special treatment because the SQL syntax is different
				// i.e. table.column IS NULL rather than table.column = null
				if ($this->value !== null)
				{ 
					// ANSI SQL functions get inserted right into SQL (not escaped, etc.)
					if ($this->value === criteria::CURRENT_DATE() || $this->value === criteria::CURRENT_TIME())
					{
						$sb .= $field . $this->comparison . $this->value;
					} 
					else
					{ 
						// default case, it is a normal col = value expression; value
						// will be replaced w/ '?' and will be inserted later using native db functions
						if ($this->ignore_string_case)
						{
							$sb .= $connection->ignore_case($field) . $this->comparison . $connection->ignore_case("?");
						} 
						else
						{
							$sb .= $field . $this->comparison . "?";
						} 
						// need to track the field in params, because
						// we'll need it to determine the correct setter
						// method later on (e.g. field 'review.DATE' => set_date());
						$params[] = array('table' => $this->table, 'column' => $this->column, 'value' => $this->value);
					} 
				} 
				else
				{ 
					// value is null, which means it was either not specified or specifically
					// set to null.
					if ($this->comparison === criteria::EQUAL() || $this->comparison === criteria::ISNULL())
					{
						$sb .= $field . criteria::ISNULL();
					} elseif ($this->comparison === criteria::NOT_EQUAL() || $this->comparison === criteria::ISNOTNULL())
					{
						$sb .= $field . criteria::ISNOTNULL();
					} 
					else
					{ 
						// for now throw an exception, because not sure how to interpret this
						return new exception(DB_ERROR, "Could not build SQL for expression: $field " . $this->comparison . " NULL");
					} 
				} 
			} 
		} 

		for($i = 0; $i < $clauses_length; $i++)
		{
			$sb .= $this->conjunctions[$i];
			$this->clauses[$i]->append_ps_to($sb, $params);
			$sb .= ')';
		} 
	} 

	/**
	* Build a string representation of the criterion.
	* 
	* @return string A String with the representation of the criterion.
	*/
	function to_string()
	{ 
		// it is alright if value == null
		
		if ($this->column == null)
		{
			return '';
		} 

		$expr = '';
		$params = array('', '');
		$this->append_ps_to($expr, $params);
		return $expr;
	} 

	/**
	* This method checks another criteria to see if they contain
	* the same attributes and hashtable entries.
	* 
	* @return boolean 
	*/
	function equals(&$crit)
	{
		if (($crit === null) || !(is_a($crit, 'criterion')))
		{
			return false;
		} 

		$is_equiv = ((($this->table === null && $crit->get_table() === null) || 
									($this->table !== null && $this->table === $crit->get_table())) && 
									$this->column === $crit->get_column() && 
									$this->comparison === $crit->get_comparison()); 
		// we need to check for value equality
		if ($is_equiv)
		{
			$is_equiv &= ($this->value === $crit->get_value());
		}
		// check chained criterion
		$clauses_length = count($this->clauses);
		$is_equiv &= (count($crit->get_clauses()) == $clauses_length);
		$crit_conjunctions = $crit->get_conjunctions();
		$crit_clauses = $crit->get_clauses();
		for ($i = 0; $i < $clauses_length; $i++)
		{
			$is_equiv &= ($this->conjunctions[$i] === $crit_conjunctions[$i]);
			$is_equiv &= ($this->clauses[$i]->equals($crit_clauses[$i]));
		} 

		return $is_equiv;
	} 

	/**
	* Returns a hash code value for the object.
	*/
	function &hash_code()
	{
		$h = crc32(serialize($this->value)) ^ crc32($this->comparison);

		if ($this->table !== null)
		{
			$h ^= crc32($this->table);
		} 

		if ($this->column !== null)
		{
			$h ^= crc32($this->column);
		} 

		$clauses_length = count($this->clauses);
		for($i = 0; $i < $clauses_length; $i++)
		{
			$h ^= crc32($this->clauses[$i]->to_string());
		} 

		return $h;
	} 

	/**
	* Get all tables from nested criterion objects
	* 
	* @return array 
	*/
	function &get_all_tables()
	{
		$tables = array();
		$this->add_criterion_table($this, $tables);
		return $tables;
	} 

	/**
	* method supporting recursion through all criterions to give
	* us a string array of tables from each criterion
	* 
	* @return void 
	* @private 
	*/
	function add_criterion_table($c, &$s)
	{
		$s[] = $c->get_table();
		$clauses = $c->get_clauses();
		$clauses_length = count($clauses);
		for($i = 0; $i < $clauses_length; $i++)
		{
			$this->add_criterion_table($clauses[$i], $s);
		} 
	} 

	/**
	* get an array of all criterion attached to this
	* recursing through all sub criterion
	* 
	* @return array criterion[]
	*/
	function &get_attached_criterion()
	{
		$crits = array();
		$this->traverse_criterion($this, $crits);
		return $crits;
	} 

	/**
	* method supporting recursion through all criterions to give
	* us an array of them
	* 
	* @param criterion $c 
	* @param array $ &$a
	* @return void 
	* @private 
	*/
	function traverse_criterion($c, &$a)
	{
		$a[] = $c;
		$clauses = $c->get_clauses();
		$clauses_length = count($clauses);
		for($i = 0; $i < $clauses_length; $i++)
		{
			$this->traverse_criterion($clauses[$i], $a);
		} 
	} 
}
?>