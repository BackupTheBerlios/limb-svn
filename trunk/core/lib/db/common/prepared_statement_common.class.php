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

require_once(LIMB_DIR . '/core/lib/db/prepared_statement.class.php');

/**
* Class that represents a shared code for handling emulated pre-compiled statements.
* 
* Many drivers do not take advantage of pre-compiling SQL statements; for these
* cases the precompilation is emulated.  This emulation comes with slight penalty involved
* in parsing the queries, but provides other benefits such as a cleaner object model and ability
* to work with BLOB and CLOB values w/o needing special LOB-specific routines.
*/
class prepared_statement_common extends prepared_statement
{
	/**
	* The database connection.
	* 
	* @var connection 
	*/
	var $conn;

	/**
	* Max rows to retrieve from DB.
	* 
	* @var int 
	*/
	var $limit = 0;

	/**
	* Offset at which to start processing DB rows.
	* "Skip X rows"
	* 
	* @var int 
	*/
	var $offset = 0;

	/**
	* The SQL this class operates on.
	* 
	* @var string 
	*/
	var $sql;

	/**
	* The string positions of the parameters in the SQL.
	* 
	* @var array 
	*/
	var $positions;

	/**
	* Number of positions (simply to save processing).
	* 
	* @var int 
	*/
	var $positions_count;

	/**
	* Map of index => value for bound params.
	* 
	* @var array string[]
	*/
	var $bound_in_vars = array();

	/**
	* Temporarily hold a result_set object after an execute() query.
	* 
	* @var result_set 
	*/
	var $result_set;

	/**
	* Temporary hold the affected row cound after an execute() query.
	* 
	* @var int 
	*/
	var $update_count;

	/**
	* Create new prepared statement instance.
	* 
	* @param object $conn connection object
	* @param string $sql The SQL to work with.
	* @param array $positions The positions in SQL of ?'s.
	* @param restult $stmt If the driver supports prepared queries, then $stmt will contain the statement to use.
	*/
	function prepared_statement_common(&$conn, &$sql)
	{
		if (! is_a($conn, 'connection'))
		{
			debug :: write_warning("parameter 1 not of type 'connection' !",
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		} 

		$this->conn = &$conn;
		$this->sql = &$sql; 
		// get the positiosn for the '?' in the SQL
		// we can move this into its own method if it gets more complex
		$positions = array();
		$positions_count = 0;
		$position = 0;

		while ($position < strlen($sql) && ($question = strpos($sql, '?', $position)) !== false)
		{
			$positions[] = $question;
			$position = $question + 1;
			$positions_count++;
		} 

		$this->positions = $positions;
		$this->positions_count = $positions_count; // save processing later in cases where we may repeatedly exec statement
	} 

	/**
	* 
	* @see prepared_statement::set_limit()
	*/
	function set_limit($v)
	{
		$this->limit = (int) $v;
	} 

	/**
	* 
	* @see prepared_statement::get_limit()
	*/
	function get_limit()
	{
		return $this->limit;
	} 

	/**
	* 
	* @see prepared_statement::set_offset()
	*/
	function set_offset($v)
	{
		$this->offset = (int) $v;
	} 

	/**
	* 
	* @see prepared_statement::get_offset()
	*/
	function get_offset()
	{
		return $this->offset;
	} 

	/**
	* 
	* @see prepared_statement::get_result_set()
	*/
	function &get_result_set()
	{
		return $this->result_set;
	} 

	/**
	* 
	* @see prepared_statement::get_update_count()
	*/
	function get_update_count()
	{
		return $this->update_count;
	} 

	/**
	* SQLite doesn't support multiple resultsets returned by single query, so this just returns false.
	* 
	* @see prepared_statement::get_connection()
	*/
	function get_more_results()
	{
		if ($this->result_set) 
			$this->result_set->close();
			
		$this->result_set = null;
		return false;
	} 

	/**
	* 
	* @see prepared_statement::get_connection()
	*/
	function &get_connection()
	{
		return $this->conn;
	} 

	/**
	* Statement resources do not exist for emulated prepared statements,
	* so this just returns <code>null</code>.
	* 
	* @return null 
	*/
	function get_resource()
	{
		return null;
	} 

	/**
	* Nothing to close for emulated prepared statements.
	*/
	function close()
	{
	} 

	/**
	* Replaces placeholders with the specified parameter values in the SQL.
	* 
	* This is for emulated prepared statements.
	* 
	* @return mixed New SQL statement with parameters replaced or sql_exception - if param not bound.
	*/
	function replace_params()
	{ 
		// Default behavior for this function is to behave in 'emulated' mode.
		$sql = '';
		$last_position = 0;

		for ($position = 0; $position < $this->positions_count; $position++)
		{
			if (!isset($this->bound_in_vars[$position + 1]))
			{
				return new sql_exception(DB_ERROR, 'Replace params: undefined query param: ' . ($position + 1));
			} 
			$current_position = $this->positions[$position];
			$sql .= substr($this->sql, $last_position, $current_position - $last_position);
			$sql .= $this->bound_in_vars[$position + 1];
			$last_position = $current_position + 1;
		} 
		// append the rest of the query
		$sql .= substr($this->sql, $last_position);

		return $sql;
	} 

	/**
	* Executes the SQL query in this prepared_statement object and returns the resultset generated by the query.
	* We support two signatures for this method:
	* - $stmt->execute_query(result_set::FETCHMODE_NUM);
	* - $stmt->execute_query(array($param1, $param2), result_set::FETCHMODE_NUM);
	* 
	* @param mixed $p1 Either (array) Parameters that will be set using prepared_statement::set() before query is executed or (int) fetchmode.
	* @param int $fetchmode The mode to use when fetching the results (e.g. result_set::FETCHMODE_NUM, result_set::FETCHMODE_ASSOC).
	* @return result_set or sql_exception if a database access error occurs.
	*/
	function &execute_query($p1 = null, $fetchmode = null)
	{
		$params = null;
		if ($fetchmode !== null)
		{
			$params = $p1;
		} 
		elseif ($p1 !== null)
		{
			if (is_array($p1)) 
				$params = $p1;
			else 
				$fetchmode = $p1;
		} 

		if ($params)
		{
			for($i = 0, $cnt = count($params); $i < $cnt; $i++)
			{
				$this->set($i + 1, $params[$i]);
			} 
		} 

		$this->update_count = null; // reset
		$sql = $this->replace_params();

		if (is_error($sql))
		{
			return $sql;
		} 

		if ($this->limit > 0)
		{
			$this->conn->apply_limit($sql, $this->offset, $this->limit);
		} 
		elseif ($this->offset > 0)
		{
			return new sql_exception(DB_ERROR, 'Cannot specify an offset without limit.');
		} 

		$this->result_set = &$this->conn->execute_query($sql, $fetchmode);
		return $this->result_set;
	} 

	/**
	* Executes the SQL INSERT, UPDATE, or DELETE statement in this prepared_statement object.
	* 
	* @param array $params Parameters that will be set using prepared_statement::set() before query is executed.
	* @return int Number of affected rows (or 0 for drivers that return nothing).
	* @throws sql_exception if a database access error occurs.
	*/
	function &execute_update($params = null)
	{
		if ($params)
		{
			for($i = 0, $cnt = count($params); $i < $cnt; $i++)
			{
				$this->set($i + 1, $params[$i]);
			} 
		} 

		if ($this->result_set) $this->result_set->close();
		$this->result_set = null; // reset
		$sql = $this->replace_params();

		if (is_error($sql))
		{
			return $sql;
		} 

		$this->update_count = &$this->conn->execute_update($sql);
		return $this->update_count;
	} 

	/**
	* Escapes special characters (usu. quotes) using native driver function.
	* 
	* @param string $str The input string.
	* @return string The escaped string.
	*/
	function escape(&$str)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* A generic set method.
	* 
	* You can use this if you don't want to concern yourself with the details.  It involves
	* slightly more overhead than the specific settesr, since it grabs the PHP type to determine
	* which method makes most sense.
	* 
	* @param int $param_index 
	* @param mixed $value 
	* @return mixed TRUE on success, sql_exception on error.
	*/
	function set($param_index, $value)
	{
		$type = gettype($value);
		if ($type == 'object')
		{
			if (is_a($value, 'blob'))
			{
				$this->set_blob($param_index, $value);
			} 
			elseif (is_a($value, 'clob'))
			{
				$this->set_clob($param_index, $value);
			} 
			elseif (is_a($value, 'date'))
			{ 
				// can't be sure if the column type is a DATE, TIME, or TIMESTAMP column
				// we'll just use TIMESTAMP by default; hopefully DB won't complain (if
				// it does, then this method just shouldn't be used).
				$this->set_timestamp($param_index, $value);
			} 
			else
			{
				return new sql_exception(DB_ERROR_UNSUPPORTED, "Unsupported object type passed to set(): " . get_class($value));
			} 
		} 
		else
		{
			if ($type == 'integer')
			{
				$type = 'int';
			} 
			elseif ($type == 'double')
			{
				$type = 'float';
			} 
			$setter = 'set_' . strtolower($type); // PHP types are case-insensitive, but we'll do this in case that changes
			$this->$setter($param_index, $value);
		} 

		return true;
	} 

	/**
	* Sets an array.
	* Unless a driver-specific method is used, this means simply serializing
	* the passed parameter and storing it as a string.
	* 
	* @param int $param_index 
	* @param array $value 
	* @return void 
	*/
	function set_array($param_index, $value)
	{
		if ($value === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = "'" . $this->escape(serialize($value)) . "'";
		} 
	} 

	/**
	* Sets a boolean value.
	* Default behavior is true = 1, false = 0.
	* 
	* @param int $param_index 
	* @param boolean $value 
	* @return void 
	*/
	function set_boolean($param_index, $value)
	{
		if ($value === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = (int) $value;
		} 
	} 

	/**
	* 
	* @see prepared_statement::set_blob()
	*/
	function set_blob($param_index, $blob)
	{
		if ($blob === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = "'" . $this->escape($blob->get_contents()) . "'";
		} 
	} 

	/**
	* 
	* @see prepared_statement::set_clob()
	*/
	function set_clob($param_index, $clob)
	{
		if ($clob === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = "'" . $this->escape($clob->get_contents()) . "'";
		} 
	} 

	/**
	* 
	* @param int $param_index 
	* @param string $value 
	* @return void 
	*/
	function set_date($param_index, $value)
	{
		if (is_numeric($value))
			$value = date("Y-m-d", $value);
		if (is_object($value)) 
			$value = date("Y-m-d", $value->get_time());
			
		if ($value === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = "'" . $this->escape($value) . "'";
		} 
	} 

	/**
	* 
	* @param int $param_index 
	* @param double $value 
	* @return void 
	*/
	function set_decimal($param_index, $value)
	{
		if ($value === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = (float) $value;
		} 
	} 

	/**
	* 
	* @param int $param_index 
	* @param double $value 
	* @return void 
	*/
	function set_double($param_index, $value)
	{
		if ($value === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = (double) $value;
		} 
	} 

	/**
	* 
	* @param int $param_index 
	* @param float $value 
	* @return void 
	*/
	function set_float($param_index, $value)
	{
		if ($value === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = (float) $value;
		} 
	} 

	/**
	* 
	* @param int $param_index 
	* @param int $value 
	* @return void 
	*/
	function set_int($param_index, $value)
	{
		if ($value === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = (int) $value;
		} 
	} 

	/**
	* Alias for set_int()
	* 
	* @param int $param_index 
	* @param int $value 
	*/
	function set_integer($param_index, $value)
	{
		$this->set_int($param_index, $value);
	} 

	/**
	* 
	* @param int $param_index 
	* @return void 
	*/
	function set_null($param_index)
	{
		$this->bound_in_vars[$param_index] = 'NULL';
	} 

	/**
	* 
	* @param int $param_index 
	* @param string $value 
	* @return void 
	*/
	function set_string($param_index, $value)
	{
		if ($value === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = "'" . $this->escape((string) $value) . "'";
		} 
	} 

	/**
	* 
	* @param int $param_index 
	* @param string $value 
	* @return void 
	*/
	function set_time($param_index, $value)
	{
		if (is_numeric($value)) 
			$value = date("H:i:s", $value);
		elseif (is_object($value)) 
			$value = date("H:i:s", $value->get_time());

		if ($value === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = "'" . $this->escape($value) . "'";
		} 
	} 

	/**
	* 
	* @param int $param_index 
	* @param string $value 
	* @return void 
	*/
	function set_timestamp($param_index, $value)
	{
		if (is_numeric($value)) 
			$value = date('Y-m-d H:i:s', $value);
		elseif (is_object($value)) 
			$value = date("Y-m-d H:i:s", $value->get_time());

		if ($value === null)
		{
			$this->set_null($param_index);
		} 
		else
		{
			$this->bound_in_vars[$param_index] = "'" . $value . "'";
		} 
	} 
} 
