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

require_once(LIMB_DIR . '/core/lib/db/statement.class.php');

/**
* Class that contains common/shared functionality for statements.
* 
*/
class statement_common extends statement
{
	/**
	* The database connection.
	* 
	* @var connection 
	*/
	var $conn;

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
	* Array of warning objects generated by methods performed on result set.
	* 
	* @var array SQLWarning[]
	*/
	var $warnings = array();

	/**
	* The result_set class name.
	* 
	* @var string 
	*/
	var $result_class;

	/**
	* The prepared statement resource id.
	* 
	* @var resource 
	*/
	var $stmt;

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
	* Create new statement instance.
	* 
	* @param connection $conn connection object
	*/
	function statement_common(&$conn)
	{
		if (! is_a($conn, 'connection'))
		{
			debug :: write_warning("parameter 1 not of type 'connection' !",
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
			array());
		} 

		$this->conn = &$conn;
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
		$this->limit = (int) $v;
	} 

	/**
	* Returns the maximum number of rows to return or 0 for all.
	* 
	* @return int 
	*/
	function get_limit()
	{
		return $this->limit;
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
		$this->offset = (int) $v;
	} 

	/**
	* Returns the start row.
	* Offset only applies when Limit is set!
	* 
	* @return int 
	*/
	function get_offset()
	{
		return $this->offset;
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
		// do nothing here (subclasses will implement)
	} 

	/**
	* Generic execute() function has to check to see whether SQL is an update or select query.
	* 
	* If you already know whether it's a SELECT or an update (manipulating) SQL, then use
	* the appropriate method, as this one will incurr overhead to check the SQL.
	* 
	* NOTICE: This function's return values are slightly different compared to the PHP5 version.
	* 
	* @param int $fetchmode Fetchmode (only applies to queries).
	* @return mixed Number of affected rows on an update, a result set on a query or a sql_exception on error.
	*/
	function execute($sql, $fetchmode = null)
	{
		if (! $this->is_select($sql))
		{
			$this->update_count = $this->execute_update($sql);
			return $this->update_count;
		} 
		else
		{
			$this->result_set = &$this->execute_query($sql, $fetchmode);
			return $this->result_set;
		} 
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
		return $this->result_set;
	} 

	/**
	* Get update count.
	* 
	* @return int Number of records affected, or <code>null</code> if not applicable.
	*/
	function get_update_count()
	{
		return $this->update_count;
	} 

	/**
	* Returns whether the passed SQL is a SELECT statement.
	* 
	* Returns true if SQL starts with 'SELECT' but not 'SELECT INTO'.  This exists
	* to support the execute() function -- which could either execute an update or
	* a query.
	* 
	* Currently this function does not take into consideration comments, primarily
	* because there are a number of different comment options for different drivers:
	* <pre>
	*    -- SQL-defined comment, but not truly comment in Oracle
	*   # comment in mysql
	*   /* comment in mssql, others * /
	*   // comment sometimes?
	*   REM also comment ...
	* </pre>
	* 
	* If you're wondering why we can't just execute the query and look at the return results
	* to see whether it was an update or a select, the reason is that for update queries we
	* need to do stuff before we execute them -- like start transactions if auto-commit is off.
	* 
	* @param string $sql 
	* @return boolean Whether statement is a SELECT SQL statement.
	* @see execute
	*/
	function is_select($sql)
	{ 
		// is first word is SELECT, then return true, unless it's SELECT INTO ...
		// this doesn't, however, take comments into account ...
		$sql = trim(strtolower($sql));
		return (strpos($sql, 'select') === 0 && strpos($sql, 'select into ') !== 0);
	} 

	/**
	* Executes the SQL query in this prepared_statement object and returns the resultset generated by the query.
	* 
	* @param string $sql This method may optionally be called with the SQL statement.
	* @param int $fetchmode The mode to use when fetching the results (e.g. result_set::FETCHMODE_NUM, result_set::FETCHMODE_ASSOC).
	* @return object Creole::result_set
	* @throws sql_exception If there is an error executing the specified query.
	*/
	function &execute_query($sql, $fetchmode = null)
	{
		$this->update_count = null;
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
	* @param string $sql This method may optionally be called with the SQL statement.
	* @return int Number of affected rows (or 0 for drivers that return nothing).
	* @return object sql_exception if a database access error occurs.
	*/
	function execute_update($sql)
	{
		if ($this->result_set) 
			$this->result_set->close();
		$this->result_set = null;
		$this->update_count = $this->conn->execute_update($sql);
		return $this->update_count;
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
		if ($this->result_set) 
			$this->result_set->close();
		$this->result_set = null;
		return false;
	} 

	/**
	* Gets the db connection that created this statement.
	* 
	* @return connection 
	*/
	function &get_connection()
	{
		return $this->conn;
	} 
} 
