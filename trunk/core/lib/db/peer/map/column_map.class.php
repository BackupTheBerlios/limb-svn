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
* column_map is used to model a column of a table in a database.
*/
class column_map
{
	/**
	* 
	* @var int db type for this column.
	*/
	var $db_type;

	/**
	* 
	* @var string Native PHP type of the column.
	*/
	var $type = null;

	/**
	* * Size of the column.
	*/
	var $size = 0;

	/**
	* * Is it a primary key?
	*/
	var $pk = false;

	/**
	* * Is null value allowed ?
	*/
	var $not_null = false;

	/**
	* * Name of the table that this column is related to.
	*/
	var $related_table_name = '';

	/**
	* * Name of the column that this column is related to.
	*/
	var $related_column_name = '';

	/**
	* * The table_map for this column.
	*/
	var $table;

	/**
	* * The name of the column.
	*/
	var $column_name;

	/**
	* * The php name of the column.
	*/
	var $php_name;

	/**
	* * validators for this column
	*/
	var $validators = array();

	/**
	* Constructor.
	* 
	* @param string $name The name of the column.
	* @param table_map $ containing_table table_map of the table this column is in.
	*/
	function column_map($name, &$containing_table)
	{
		$this->column_name = $name;
		$this->table = &$containing_table;
	} 

	/**
	* Get the name of a column.
	* 
	* @return string A String with the column name.
	*/
	function get_column_name()
	{
		return $this->column_name;
	} 

	/**
	* Set the php anme of this column.
	* 
	* @param string $php_name A string representing the PHP name.
	* @return void 
	*/
	function set_php_name($php_name)
	{
		$this->php_name = $php_name;
	} 

	/**
	* Get the name of a column.
	* 
	* @return string A String with the column name.
	*/
	function get_php_name()
	{
		return $this->php_name;
	} 

	/**
	* Get the table name + column name.
	* 
	* @return string A String with the full column name.
	*/
	function get_fully_qualified_name()
	{
		return $this->table->get_name() . '.' . $this->column_name;
	} 

	/**
	* Get the name of the table this column is in.
	* 
	* @return string A String with the table name.
	*/
	function get_table_name()
	{
		return $this->table->get_name();
	} 

	/**
	* Set the type of this column.
	* 
	* @param string $type A string representing the PHP native type.
	* @return void 
	*/
	function set_type($type)
	{
		$this->type = $type;
	} 

	/**
	* Set the db type of this column.
	* 
	* @param int $type An int representing db type for this column.
	* @return void 
	*/
	function set_db_type($type)
	{
		$this->db_type = $type;
	} 

	/**
	* Set the size of this column.
	* 
	* @param int $size An int specifying the size.
	* @return void 
	*/
	function set_size($size)
	{
		$this->size = $size;
	} 

	/**
	* Set if this column is a primary key or not.
	* 
	* @param boolean $pk True if column is a primary key.
	* @return void 
	*/
	function set_primary_key($pk)
	{
		$this->pk = $pk;
	} 

	/**
	* Set if this column may be null.
	* 
	* @param boolean $ nn True if column may be null.
	* @return void 
	*/
	function set_not_null($nn)
	{
		$this->not_null = $nn;
	} 

	/**
	* Set the foreign key for this column.
	* 
	* @param string $ table_name The name of the table that is foreign.
	* @param string $ column_name The name of the column that is foreign.
	* @return void 
	*/
	function set_foreign_key($table_name, $column_name)
	{
		if ($table_name && $column_name)
		{
			$this->related_table_name = $table_name;
			$this->related_column_name = $column_name;
		} 
		else
		{
			$this->related_table_name = '';
			$this->related_column_name = '';
		} 
	} 

	function add_validator(&$validator)
	{
		$this->validators[] = &$validator;
	} 

	function has_validators()
	{
		return count($this->validators) > 0;
	} 

	function &get_validators()
	{
		return $this->validators;
	} 

	/**
	* Get the native PHP type of this column.
	* 
	* @return string A string specifying the native PHP type.
	*/
	function get_type()
	{
		return $this->type;
	} 

	/**
	* Get the db type of this column.
	* 
	* @return string A string specifying the native PHP type.
	*/
	function get_db_type()
	{
		return $this->db_type;
	} 

	/**
	* Get the size of this column.
	* 
	* @return int An int specifying the size.
	*/
	function get_size()
	{
		return $this->size;
	} 

	/**
	* Is this column a primary key?
	* 
	* @return boolean True if column is a primary key.
	*/
	function is_primary_key()
	{
		return $this->pk;
	} 

	/**
	* Is null value allowed ?
	* 
	* @return boolean True if column may be null.
	*/
	function is_not_null()
	{
		return ($this->not_null || $this->is_primary_key());
	} 

	/**
	* Is this column a foreign key?
	* 
	* @return boolean True if column is a foreign key.
	*/
	function is_foreign_key()
	{
		if ($this->related_table_name)
		{
			return true;
		} 
		else
		{
			return false;
		} 
	} 

	/**
	* Get the table.column that this column is related to.
	* 
	* @return string A String with the full name for the related column.
	*/
	function get_related_name()
	{
		return $this->related_table_name . "." . $this->related_column_name;
	} 

	/**
	* Get the table name that this column is related to.
	* 
	* @return string A String with the name for the related table.
	*/
	function get_related_table_name()
	{
		return $this->related_table_name;
	} 

	/**
	* Get the column name that this column is related to.
	* 
	* @return string A String with the name for the related column.
	*/
	function get_related_column_name()
	{
		return $this->related_column_name;
	} 
} 
