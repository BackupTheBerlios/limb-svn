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
* Class that represents a SQL statement.
* 
* This class is very generic and has no driver-specific implementations.  In fact,
* it wouldn't be possible to have driver-specific classes, since PHP doesn't support
* multiple inheritance.  I.e. you couldn't have mysql_prepared_statement that extended
* both the abstract prepared_statement class and the mysql_statement class.  In Java
* this isn't a concern since prepared_statement is an interface, not a class.
*/
class statement
{
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
	* Generic execute() function has to check to see whether SQL is an update or select query.
	* 
	* If you already know whether it's a SELECT or an update (manipulating) SQL, then use
	* the appropriate method, as this one will incurr overhead to check the SQL.
	* 
	* @param int $fetchmode Fetchmode (only applies to queries).
	* @return boolean True if it is a result set, false if not or if no more results (this is identical to JDBC return val).
	* @throws sql_exception
	*/
	function execute($sql, $fetchmode = null)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get result set.
	* This assumes that the last thing done was an execute_query() or an execute()
	* with SELECT-type query.
	* 
	* @return restult_set (or null if none)
	*/
	function &get_result_set()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get update count.
	* 
	* @return int Number of records affected, or <code>null</code> if not applicable.
	*/
	function &get_update_count()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Executes the SQL query in this prepared_statement object and returns the resultset generated by the query.
	* 
	* @param string $sql This method may optionally be called with the SQL statement.
	* @param int $fetchmode The mode to use when fetching the results (e.g. result_set::FETCHMODE_NUM, result_set::FETCHMODE_ASSOC).
	* @return object Creole::result_set
	* @throws sql_exception if a database access error occurs.
	*/
	function &execute_query($sql, $fetchmode = null)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Executes the SQL INSERT, UPDATE, or DELETE statement in this prepared_statement object.
	* 
	* @param string $sql This method may optionally be called with the SQL statement.
	* @return int Number of affected rows (or 0 for drivers that return nothing).
	* @throws sql_exception if a database access error occurs.
	*/
	function execute_update($sql)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Gets next result set (if this behavior is supported by driver).
	* Some drivers (e.g. MSSQL) support returning multiple result sets -- e.g.
	* from stored procedures.
	* 
	* This function also closes any current result set.
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
	* Gets the db Connection that created this statement.
	* 
	* @return Connection 
	*/
	function &get_connection()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 
} 
