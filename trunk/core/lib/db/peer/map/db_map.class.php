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

require_once(LIMB_DIR . '/core/lib/db/peer/map/table_map.class.php');

/**
* database_map is used to model a database.
*/
class database_map
{
	/**
	* * Name of the database.
	*/
	var $name;

	/**
	* * Name of the tables in the database.
	*/
	var $tables;

	/**
	* Constructor.
	* 
	* @param string $name Name of the database.
	*/
	function database_map($name)
	{
		$this->name = $name;
		$this->tables = array();
	} 

	/**
	* Does this database contain this specific table?
	* 
	* @param string $name The String representation of the table.
	* @return boolean True if the database contains the table.
	*/
	function contains_table($name)
	{
		if (strpos($name, '.') > 0)
		{
			$name = substr($name, 0, strpos($name, '.'));
		} 

		return isset($this->tables[$name]);
	} 

	/**
	* Get the name of this database.
	* 
	* @return string The name of the database.
	*/
	function get_name()
	{
		return $this->name;
	} 

	/**
	* Get a table_map for the table by name.
	* 
	* @param string $name Name of the table.
	* @return table_map A table_map, null if the table was not found.
	*/
	function get_table($name)
	{
		if (isset($this->tables["$name"]))
		{
			return $this->tables["$name"];
		} 

		return null;
	} 

	/**
	* Get a table_map[] of all of the tables in the database.
	* 
	* @return array A table_map[].
	*/
	function get_tables()
	{
		return $this->tables;
	} 

	/**
	* Add a new table to the database by name.  It creates an empty
	* table_map that you need to populate.
	* 
	* @param string $table_name The name of the table.
	* @return table_map The newly created table_map.
	*/
	function &add_table($table_name)
	{
		$this->tables[$table_name] = &new table_map($table_name, $this);
		return $this->tables[$table_name];
	} 
} 
