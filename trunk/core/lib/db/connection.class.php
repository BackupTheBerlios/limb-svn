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

// we need this for the fetchmode result_set flags (constants) that are passed to execute_query()
require_once(LIMB_DIR . '/core/lib/db/result_set.class.php');

/**
* connection is an abstract base class for DB dialect implementations, and must be
* inherited by all such.
* 
* Developer notes:
*   (1) Make sure that your connection class can be serialized.  See the connection_common __sleep() and __wakeup() implimentation. 
*/
class connection
{ 
	// Constants that define transaction isolation levels.
	// [We don't have any code using these yet, so there's no need
	// to initialize these values at this point.]
	// const TRANSACTION_NONE = 0;
	// const TRANSACTION_READ_UNCOMMITTED = 1;
	// const TRANSACTION_READ_COMMITTED = 2;
	// const TRANSACTION_REPEATABLE_READ = 3;
	// const TRANSACTION_SERIALIZABLE = 4;
	/**
	* Connect to a database and log in as the specified user.
	* 
	* @param array $dsn The PEAR-style data source hash.
	* @param int $flags (optional) Flags for connection (e.g. db_factory::PERSISTENT()).  These flags
	*                    may apply to any of the driver classes.
	* @return TRUE on success, sql_exception on error.
	*/
	function connect($dsn, $flags = false)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get the PHP native resource for the database connection/link.
	* 
	* @return resource 
	*/
	function get_resource()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get any flags that were passed to connection.
	* 
	* @return int 
	*/
	function get_flags()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get the DSN array used by connect() method to connect to database.
	* 
	* @see connect
	* @return array 
	*/
	function get_dsn()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Gets a db_info class for the current database.
	* 
	* This is not modeled on the JDBC MetaData class, but provides a possibly more
	* useful metadata system.  All the same, there may eventually be a getMetaData()
	* which returns a class that behaves like JDBC's DatabaseMetaData.
	* 
	* @return db_info 
	*/
	function get_db_info()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Loads and returns an IdGenerator object for current RDBMS.
	* 
	* @return IdGenerator 
	*/
	function get_id_generator()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Prepares a query for multiple execution with execute().
	* 
	* With some database backends, this is emulated.
	* prepare() requires a generic query as string like
	* "INSERT INTO numbers VALUES(?,?,?)". The ? are placeholders.
	* 
	* IMPORTANT:  All occurrences of the placeholder (?) will be assumed
	* to be a parameter.  Therefore be sure not to have ? anywhere else in
	* the query.
	* 
	* So, ... DO NOT MIX WILDCARDS WITH ALREADY-PREPARED QUERIES
	* 
	* INCORRECT:
	*    SELECT * FROM mytable WHERE id = ? AND title = 'Where are you?' and body LIKE ?
	* 
	* CORRECT:
	*   SELECT * FROM mytable WHERE id = ? AND title = ? and body LIKE ?
	* 
	* @param string $sql The query to prepare.
	* @return PreparedStatement 
	* @throws sql_exception
	* @see PreparedStatement::execute()
	*/
	function prepare_statement($sql)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Creates a new empty Statement.
	* 
	* @return Statement 
	*/
	function create_statement()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* If RDBMS supports native LIMIT/OFFSET then query SQL is modified
	* so that no emulation is performed in result_set.
	* 
	* @param string $ &$sql The query that will be modified.
	* @param int $offset 
	* @param int $limit 
	* @return void 
	* @throws sql_exception - if unable to modify query for any reason.
	*/
	function apply_limit(&$sql, $offset, $limit)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Executes the SQL query in this PreparedStatement object and returns the resultset.
	* 
	* @param string $sql The SQL statement.
	* @param int $fetchmode 
	* @return object result_set
	* @throws sql_exception if a database access error occurs.
	*/
	function execute_query($sql, $fetchmode = null)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Executes the SQL INSERT, UPDATE, or DELETE statement.
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
	* Creates a CallableStatement object for calling database stored procedures.
	* 
	* @param string $sql 
	* @return CallableStatement 
	*/
	function prepare_call($sql)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Free the db resources.
	* 
	* @return void 
	*/
	function close()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get auto-commit status.
	* 
	* @return boolean 
	*/
	function get_auto_commit()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Enable/disable automatic commits.
	* 
	* Pushes SQLWarning onto $warnings stack if the autocommit value is being changed mid-transaction. This function
	* is overridden by driver classes so that they can perform the necessary begin/end transaction SQL.
	* 
	* If auto-commit is being set to TRUE, then the current transaction will be committed immediately.
	* 
	* @param boolean $bit New value for auto commit.
	* @return void 
	*/
	function set_auto_commit($bit)
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Commits statements in a transaction.
	* 
	* @return TRUE on success, sql_exception on error.
	*/
	function commit()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Rollback changes in a transaction.
	* 
	* @return TRUE on success, sql_exception on error.
	*/
	function rollback()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Gets the number of rows affected by the data manipulation
	* query.
	* 
	* @return int Number of rows affected by the last query.
	*/
	function get_update_count()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 
} 
