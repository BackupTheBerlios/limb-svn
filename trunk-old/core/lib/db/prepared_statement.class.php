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
* Interface for a pre-compiled SQL statement.
* 
* Many drivers do not take advantage of pre-compiling SQL statements; for these
* cases the precompilation is emulated.  This emulation comes with slight penalty involved
* in parsing the queries, but provides other benefits such as a cleaner object model and ability
* to work with BLOB and CLOB values w/o needing special LOB-specific routines.
* 
* This class is abstract because there are driver-specific implementations in [clearly] how queries
* are executed, and how parameters are bound.
* 
* This class is not as abstract as the JDBC version.  For exmple, if you are using a driver
* that uses name-based query param substitution, then you'd better bind your variables to
* names rather than index numbers.  e.g. in Oracle
* <code>
*             $stmt = $conn->prepare_statement("INSERT INTO users (name, passwd) VALUES (:name, :pass)");
*             $stmt->set_string(":name", $name);
*             $stmt->execute_update();
* </code>
* 
* Developer note:  In many ways this interface is an extension of the Statement interface.  However, due
* to limitations in PHP5's interface extension model (specifically that you cannot change signatures on
* methods defined in parent interface), we cannot extend the Statement interface.
* 
*/
class prepared_statement
{
	/**
	* Gets the db Connection that created this statement.
	* 
	* @return Connection 
	*/
	function &get_connection()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get the PHP native resource for the statement (if supported).
	* 
	* @return resource 
	*/
	function &get_resource()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Free resources associated with this statement.
	* Some drivers will need to implement this method to free
	* database result resources.
	* 
	* @return void 
	*/
	function close()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get result set.
	* This assumes that the last thing done was an execute_query() or an execute()
	* with SELECT-type query.
	* 
	* @return restult_set Last result_set or <code>null</code> if not applicable.
	*/
	function &get_result_set()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Gets next result set (if this behavior is supported by driver).
	* Some drivers (e.g. MSSQL) support returning multiple result sets -- e.g.
	* from stored procedures.
	* 
	* This function also closes any current restult set.
	* 
	* Default behavior is for this function to return false.  Driver-specific
	* implementations of this class can override this method if they actually
	* support multiple result sets.
	* 
	* @return boolean True if there is another result set, otherwise false.
	*/
	function get_more_results()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get update count.
	* 
	* @return int Number of records affected, or <code>null</code> if not applicable.
	*/
	function get_update_count()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Sets the maximum number of rows to return from db.
	* This will affect the SQL if the RDBMS supports native LIMIT; if not,
	* it will be emulated.  Limit only applies to queries (not update sql).
	* 
	* @param int $v Maximum number of rows or 0 for all rows.
	* @return void 
	*/
	function set_limit($v)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Returns the maximum number of rows to return or 0 for all.
	* 
	* @return int 
	*/
	function get_limit()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Sets the start row.
	* This will affect the SQL if the RDBMS supports native OFFSET; if not,
	* it will be emulated. Offset only applies to queries (not update) and
	* only is evaluated when LIMIT is set!
	* 
	* @param int $v 
	* @return void 
	*/
	function set_offset($v)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Returns the start row.
	* Offset only applies when Limit is set!
	* 
	* @return int 
	*/
	function get_offset()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Executes the SQL query in this prepared_statement object and returns the resultset generated by the query.
	* We support two signatures for this method:
	* - $stmt->execute_query(result_set::FETCHMODE_NUM());
	* - $stmt->execute_query(array($param1, $param2), result_set::FETCHMODE_NUM());
	* 
	* @param mixed $p1 Either (array) Parameters that will be set using prepared_statement::set() before query is executed or (int) fetchmode.
	* @param int $fetchmode The mode to use when fetching the results (e.g. result_set::FETCHMODE_NUM(), result_set::FETCHMODE_ASSOC()).
	* @return result_set 
	* @throws sql_exception if a database access error occurs.
	*/
	function &execute_query($p1 = null, $fetchmode = null)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Executes the SQL INSERT, UPDATE, or DELETE statement in this prepared_statement object.
	* 
	* @param array $params Parameters that will be set using prepared_statement::set() before query is executed.
	* @return int Number of affected rows (or 0 for drivers that return nothing).
	* @throws sql_exception if a database access error occurs.
	*/
	function execute_update($params = null)
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
	* @return void 
	* @throws sql_exception
	*/
	function set($param_index, $value)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
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
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
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
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param int $param_index 
	* @param mixed $blob Blob object or string containing data.
	* @return void 
	*/
	function set_blob($param_index, $blob)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param int $param_index 
	* @param mixed $clob Clob object  or string containing data.
	* @return void 
	*/
	function set_clob($param_index, $clob)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param int $param_index 
	* @param string $value 
	* @return void 
	*/
	function set_date($param_index, $value)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param int $param_index 
	* @param float $value 
	* @return void 
	*/
	function set_float($param_index, $value)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param int $param_index 
	* @param int $value 
	* @return void 
	*/
	function set_int($param_index, $value)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param int $param_index 
	* @return void 
	*/
	function set_null($param_index)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param int $param_index 
	* @param string $value 
	* @return void 
	*/
	function set_string($param_index, $value)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param int $param_index 
	* @param string $value 
	* @return void 
	*/
	function set_time($param_index, $value)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @param int $param_index 
	* @param string $value 
	* @return void 
	*/
	function set_timestamp($param_index, $value)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 
} 
