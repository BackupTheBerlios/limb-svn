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
* Represents a table.
* 
*/
class table_info
{ 
	var $name;
	var $columns = array();
	var $foreign_keys = array();
	var $indexes = array();
	var $primary_key;

	var $primary_key_loaded = false;
	var $fks_loaded = false;
	var $indexes_loaded = false;
	var $cols_loaded = false;

	/**
	* Database Connection.
	* 
	* @var Connection 
	*/
	var $conn;

	/**
	* The parent db_info object.
	* 
	* @var db_info 
	*/
	var $database;

	/**
	* * Shortcut to db resource link id (needed by drivers for queries).
	*/
	var $dblink;

	/**
	* * Shortcut to db name (needed by many drivers for queries).
	*/
	var $dbname;

	/**
	* 
	* @param string $table The table name.
	* @param string $database The database name.
	* @param resource $dblink The db connection resource.
	*/
	function table_info(&$database, $name)
	{
		if (! is_a($database, 'db_info'))
		{
			trigger_error("parameter 1 not of type 'db_info'", E_USER_WARNING);
		} 
		$this->database = &$database;
		$this->name = $name;
		$this->conn = &$database->get_connection(); // shortcut because all drivers need this for the queries
		$this->dblink = &$this->conn->get_resource();
		$this->dbname = $database->get_name();
	} 

	/**
	* This "magic" method is invoked upon serialize().
	* Because the info class hierarchy is recursive, we must handle
	* the serialization and unserialization of this object.
	* 
	* @return array The class variables that should be serialized (all must be public!).
	*/
	function __sleep()
	{
		return array('name', 'columns', 'foreign_keys', 'indexes', 'primary_key');
	} 

	/**
	* This "magic" method is invoked upon unserialize().
	* This method re-hydrates the object and restores the recursive hierarchy.
	*/
	function __wakeup()
	{ 
		// restore chaining
		foreach($this->columns as $col)
		{
			$col->table = $this;
		} 
	} 

	/**
	* Loads the columns.
	* 
	* @return void 
	*/
	function init_columns()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Loads the primary key information for this table.
	* 
	* @return void 
	*/
	function init_primary_key()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Loads the foreign keys for this table.
	* 
	* @return void 
	*/
	function init_foreign_keys()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Loads the indexes information for this table.
	* 
	* @return void 
	*/
	function init_indexes()
	{
		error('abstract function',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
	} 

	/**
	* Get parimary key in this table.
	* 
	* @throws Exception - if foreign keys are unsupported by DB.
	* @return array foreign_key_info[]
	*/
	function &get_primary_key()
	{
		if (!$this->primary_key_loaded) 
			$this->init_primary_key();
		return $this->primary_key;
	} 

	/**
	* Get the column_info object for specified column.
	* 
	* @param string $name The column name.
	* @return column_info 
	* @throws sql_exception - if column does not exist for this table.
	*/
	function &get_column($name)
	{
		if (!$this->cols_loaded) 
			$this->init_columns();
			
		if (!isset($this->columns[$name]))
		{
			return new sql_exception(DB_ERROR_NOSUCHFIELD, "Table `" . $this->name . "` has no column `" . $name . "`");
		} 
		return $this->columns[$name];
	} 

	/**
	* Get array of columns for this table.
	* 
	* @return array column_info[]
	*/
	function &get_columns()
	{
		if (!$this->cols_loaded) 
			$this->init_columns();
		return array_values($this->columns); // re-key numerically
	} 

	/**
	* Get specified fk for this table.
	* 
	* @param string $name The foreign key name to retrieve.
	* @return foreign_key_info 
	* @throws sql_exception - if fkey does not exist for this table.
	*/
	function &get_foreign_key($name)
	{
		if (!$this->fks_loaded) 
			$this->init_foreign_keys();
		if (!isset($this->foreign_keys[$name]))
		{
			return new sql_exception(DB_ERROR_NOSUCHFIELD, "Table `" . $this->name . "` has no foreign key `" . $name . "`");
		} 
		return $this->foreign_keys[$name];
	} 

	/**
	* Get all foreign keys.
	* 
	* @return array foreign_key_info[]
	*/
	function &get_foreign_keys()
	{
		if (!$this->fks_loaded) 
			$this->init_foreign_keys();
		return array_values($this->foreign_keys);
	} 

	/**
	* Gets the index_info object for a specified index.
	* 
	* @param string $name The index name to retrieve.
	* @return index_info 
	* @throws sql_exception - if index does not exist for this table.
	*/
	function &get_index($name)
	{
		if (!$this->indexes_loaded) 
			$this->init_indexes();
		if (!isset($this->indexes[$name]))
		{
			return new sql_exception(DB_ERROR_NOSUCHFIELD, "Table `" . $this->name . "` has no index `" . $name . "`");
		} 
		return @$this->indexes[$name];
	} 

	/**
	* Get array of index_info objects for this table.
	* 
	* @return array index_info[]
	*/
	function &get_indexes()
	{
		if (!$this->indexes_loaded) 
			$this->init_indexes();
		return array_values($this->indexes);
	} 

	/**
	* Alias for get_indexes() method.
	* 
	* @return array 
	*/
	function &get_indices()
	{
		return $this->get_indexes();
	} 

	/**
	* Get table name.
	* 
	* @return string 
	*/
	function get_name()
	{
		return $this->name;
	} 

	/**
	* 
	* @return string 
	*/
	function to_string()
	{
		return $this->name;
	} 

	/**
	* * Have foreign keys been loaded?
	*/
	function foreign_keys_loaded()
	{
		return $this->fks_loaded;
	} 

	/**
	* * Has primary key info been loaded?
	*/
	function primary_key_loaded()
	{
		return $this->primary_key_loaded;
	} 

	/**
	* * Have columns been loaded?
	*/
	function columns_loaded()
	{
		return $this->cols_loaded;
	} 

	/**
	* * Has index information been loaded?
	*/
	function indexes_loaded()
	{
		return $this->indexes_loaded;
	} 

	/**
	* * Adds a column to this table.
	*/
	function add_column(&$column)
	{
		if (! is_a($column, 'column_info'))
		{
			debug :: write_warning("parameter 1 not of type 'column_info'",
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		} 
		$this->columns[$column->get_name()] = &$column;
	} 

	/**
	* * Get the parent db_info object.
	*/
	function &get_database()
	{
		return $this->database;
	} 
} 
