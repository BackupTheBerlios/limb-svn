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

require_once(LIMB_DIR . '/core/lib/db/connection.class.php');

/**
* Class that contains some shared/default information for connections.  Classes may wish to extend this so
* as not to worry about the sleep/wakeup methods, etc.
* 
* In reality this class is not very useful yet, so there's not much incentive for drivers to extend this.
* 
*/
class connection_common extends connection
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
	* The depth level of current transaction.
	* 
	* @var int 
	*/
	var $transaction_opcount = 0;

	/**
	* 
	* @var boolean 
	*/
	var $autocommit = true;

	/**
	* DB connection resource id.
	* 
	* @var resource 
	*/
	var $dblink;

	/**
	* Array hash of connection properties.
	* 
	* @var array 
	*/
	var $dsn;

	/**
	* Flags (e.g. connection::PERSISTENT) for current connection.
	* 
	* @var int 
	*/
	var $flags = 0;

	/**
	* This "magic" method is invoked upon serialize() and works in tandem with the __wakeup()
	* method to ensure that your database connection is serializable.
	* 
	* This method returns an array containing the names of any members of your class
	* which need to be serialized in order to allow the class to re-connect to the database
	* when it is unserialized.
	* 
	* <p>
	* Developers:
	* 
	* Note that you cannot serialize resources (connection links) and expect them to
	* be valid when you unserialize.  For this reason, you must re-connect to the database in the
	* __wakeup() method.
	* 
	* It's up to your class implimentation to ensure that the necessary data is serialized.
	* You probably at least need to serialize:
	* 
	*   (1) the DSN array used by connect() method
	*   (2) Any flags that were passed to the connection
	*   (3) Possibly the autocommit state
	* 
	* @return array The class variable names that should be serialized.
	* @see __wakeup
	* @see db_info::__sleep()
	*/
	function __sleep()
	{
		return array('dsn', 'flags');
	} 

	/**
	* This "magic" method is invoked upon unserialize().
	* This method will re-connects to the database using the information that was
	* stored using the __sleep() method.
	* 
	* @see __sleep
	*/
	function __wakeup()
	{
		$this->connect($this->dsn, $this->flags);
	} 

	/**
	* 
	* @see connection::get_resource()
	*/
	function get_resource()
	{
		return $this->dblink;
	} 

	/**
	* 
	* @see connection::get_dsn()
	*/
	function get_dsn()
	{
		return $this->dsn;
	} 

	/**
	* 
	* @see connection::get_flags()
	*/
	function get_flags()
	{
		return $this->flags;
	} 

	/**
	* Creates a callable_statement object for calling database stored procedures.
	* 
	* @param string $sql 
	* @return callable_statement 
	*/
	function prepare_call(&$sql)
	{
		return new sql_exception(DB_ERROR_UNSUPPORTED, "Current driver does not support stored procedures using callable_statement.");
	} 

	/**
	* If RDBMS supports native LIMIT/OFFSET then query SQL is modified
	* so that no emulation is performed in result_set.
	* 
	* By default this method adds LIMIT/OFFSET in the style
	* " LIMIT $limit OFFSET $offset"  to end of SQL.
	* 
	* @param string $ &$sql The query that will be modified.
	* @param int $offset 
	* @param int $limit 
	* @return boolean Whether the query was modified.
	* @throws sql_exception - if unable to modify query for any reason.
	*/
	function apply_limit(&$sql, $offset, $limit)
	{
		$sql .= " LIMIT " . $limit . " OFFSET " . $offset;
	} 

	/**
	* Get auto-commit status.
	* 
	* @return boolean 
	*/
	function get_auto_commit()
	{
		return $this->autocommit;
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
	* @return TRUE on success, FALSE on mid-transaction, sql_exception otherwise.
	*/
	function set_auto_commit($bit)
	{
		$this->autocommit = (boolean) $bit;
		if ($this->transaction_opcount > 0)
		{
			trigger_error("Changing autocommit in mid-transaction; committing "
				 . $this->transaction_opcount . " uncommitted statements.");

			return false;
		} 

		if ($bit && $this->transaction_opcount > 0)
		{
			return $this->commit();
		} 

		return true;
	} 
} 
