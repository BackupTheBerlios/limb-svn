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

/*
 * Represents a Column.
 *
 */
class column_info
{ 
	/**
	* * Column name
	*/
	var $name;

	/**
	* * Column db type.
	*/
	var $type;

	/**
	* * Column native type
	*/
	var $native_type;

	/**
	* * Column length
	*/
	var $size;

	/**
	* * Column scale (number of digits after decimal )
	*/
	var $scale;

	/**
	* * Is nullable?
	*/
	var $is_nullable;

	/**
	* * Default value
	*/
	var $default_value;

	/**
	* * Table
	*/
	var $table;

	/**
	* Construct a new column_info object.
	* 
	* @param table_info $table The table that owns this column.
	* @param string $name Column name.
	* @param int $type db type.
	* @param string $native_type Native type name.
	* @param int $size Column length.
	* @param int $scale Column scale (number of digits after decimal).
	* @param boolean $is_nullable Whether col is nullable.
	* @param mixed $default Default value.
	*/
	function column_info(&$table, $name, $type = null, $native_type = null, $size = null, $scale = null, $is_nullable = null, $default = null)
	{
		if (! is_a($table, 'table_info'))
		{
			debug :: write_warning("parameter 1 not of type 'table_info"',
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		} 

		$this->table = &$table;
		$this->name = $name;
		$this->type = $type;
		$this->native_type = $native_type;
		$this->size = $size;
		$this->scale = $scale;
		$this->is_nullable = $is_nullable;
		$this->default_value = $default;
	} 

	/**
	* This "magic" method is invoked upon serialize().
	* Because the Info class hierarchy is recursive, we must handle
	* the serialization and unserialization of this object.
	* 
	* @return array The class variables that should be serialized (all must be public!).
	*/
	function __sleep()
	{
		return array('name', 'type', 'native_type', 'size', 'precision', 'is_nullable', 'default_value');
	} 

	/**
	* Get column name.
	* 
	* @return string 
	*/
	function get_name()
	{
		return $this->name;
	} 

	/**
	* Get column type.
	* 
	* @return int 
	*/
	function get_type()
	{
		return $this->type;
	} 

	/**
	* Gets the native type name.
	* 
	* @return string 
	*/
	function get_native_type()
	{
		return $this->native_type;
	} 

	/**
	* Get column size.
	* 
	* @return int 
	*/
	function get_size()
	{
		return $this->size;
	} 

	/**
	* Get column scale.
	* Scale refers to number of digits after the decimal.  Sometimes this is referred
	* to as precision, but precision is the total number of digits (i.e. length).
	* 
	* @return int 
	*/
	function get_scale()
	{
		return $this->scale;
	} 

	/**
	* Get the default value.
	* 
	* @return mixed 
	*/
	function get_default_value()
	{
		return $this->default_value;
	} 

	/**
	* Is column nullable?
	* 
	* @return boolean 
	*/
	function is_nullable()
	{
		return $this->is_nullable;
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
	* Get parent table.
	* 
	* @return table_info 
	*/
	function &get_table()
	{
		return $this->table;
	} 
} 
