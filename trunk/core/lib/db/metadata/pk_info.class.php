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
* Represents a primary key
* 
*/
class pk_info
{
	/**
	* * name of the primary key
	*/
	var $name;

	/**
	* * columns in the primary key
	*/
	var $columns = array();

	/**
	* 
	* @param string $name The name of the foreign key.
	*/
	function pk_info($name)
	{
		$this->name = $name;
	} 

	/**
	* Get foreign key name.
	* 
	* @return string 
	*/
	function get_name()
	{
		return $this->name;
	} 

	/**
	* 
	* @param column $column 
	* @return void 
	*/
	function add_column(&$column)
	{
		$this->columns[] = &$column;
	} 

	/**
	* 
	* @return array column[]
	*/
	function &get_columns()
	{
		return $this->columns;
	} 

	/**
	* 
	* @return string 
	*/
	function to_string()
	{
		return $this->name;
	} 
} 
