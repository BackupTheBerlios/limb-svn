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
* "Info" metadata class for a database.
* 
*/
class db_info
{
	var $tables = array();

	var $sequences = array();

	/**
	* * have tables been loaded
	*/
	var $tables_loaded = false;

	/**
	* * have sequences been loaded
	*/
	var $seqs_loaded = false;

	/**
	* The database connection.
	* 
	* @var connection 
	*/
	var $conn;

	/**
	* * Database name.
	*/
	var $dbname;

	/**
	* Database link
	* 
	* @var resource 
	*/
	var $dblink;

	/**
	* 
	* @param connection $dbh 
	*/
	function db_info(&$conn)
	{
		if (! is_a($conn, 'connection'))
		{
			debug :: write_warning("parameter 1 not of type 'connection'",
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		} 
		$this->conn = &$conn;
		$this->dblink = &$conn->get_resource();
		$dsn = $conn->get_dsn();
		$this->dbname = $dsn['database'];
	} 

	/**
	* Get name of database.
	* 
	* @return string 
	*/
	function get_name()
	{
		return $this->dbname;
	} 

	/**
	* This method is invoked upon serialize().
	* Because the Info class hierarchy is recursive, we must handle
	* the serialization and unserialization of this object.
	* 
	* @return array The class variables that should be serialized (all must be public!).
	*/
	function __sleep()
	{
		return array('tables', 'conn');
	} 

	/**
	* This method is invoked upon unserialize().
	* This method re-hydrates the object and restores the recursive hierarchy.
	*/
	function __wakeup()
	{ 
		// Re-init vars from serialized connection
		$this->dbname = &$conn->database;
		$this->dblink = &$conn->connection; 
		// restore chaining
		foreach($this->tables as $tbl)
		{
			$tbl->database = $this;
			$tbl->dbname = $this->dbname;
			$tbl->dblink = $this->dblink;
			$tbl->schema = $this->schema;
		} 
	} 

	/**
	* Returns connection being used.
	* 
	* @return connection 
	*/
	function &get_connection()
	{
		return $this->conn;
	} 

	/**
	* Get the table_info object for specified table name.
	* 
	* @param string $name The name of the table to retrieve.
	* @return mixed table_info on success, sql_exception - if table does not exist in this db.
	*/
	function &get_table($name)
	{
		if (!$this->tables_loaded)
		{
			if (is_error($e = $this->init_tables()))
			{
				return $e;
			} 
			if (!isset($this->tables[strtoupper($name)]))
			{
				return new sql_exception(DB_ERROR_NOSUCHTABLE, "Database `" . $this->name . "` has no table `" . $name . "`");
			} 

			return $this->tables[ strtoupper($name) ];
		} 
	}
	
	/**
	* Gets array of table_info objects.
	* 
	* @return array table_info[]
	*/
	function &get_tables()
	{
		if (!$this->tables_loaded) 
			$this->init_tables();
		return array_values($this->tables); //re-key [numerically]
	} 

	/**
	* Adds a table to this db.
	* Table name is case-insensitive.
	* 
	* @param table_info $table 
	*/
	function add_table(&$table)
	{
		if (! is_a($table, 'table_info'))
		{
			debug :: write_warning("parameter 1 not of type 'table_info'",
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		} 
		$this->tables[strtoupper($table->get_name())] = &$table;
	} 

	/**
	* 
	* @return void 
	* @throws sql_exception
	*/
	function init_tables()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 
	// FIXME
	// Figure out sequences.  What are they exactly?  Simply columns?
	// Should this logic really be at the db level (yes & no, i think).  Maybe
	// also a column::is_sequence() method ?  posgre_sql supports sequences obviously,
	// but currently this part of dbinfo classes is not being used.
	/**
	* 
	* @return void 
	* @throws sql_exception
	*/
	function init_sequences()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* 
	* @return boolean 
	* @throws sql_exception
	*/
	function is_sequence($key)
	{
		if (!$this->seqs_loaded) 
			$this->init_sequences();
			
		return isset($this->sequences[ strtoupper($key) ]);
	} 

	/**
	* Gets array of ? objects.
	* 
	* @return array ?[]
	*/
	function get_sequences()
	{
		if (!$this->seqs_loaded) 
			$this->init_sequences();
		return array_values($this->sequences); //re-key [numerically]
	} 
} 

	