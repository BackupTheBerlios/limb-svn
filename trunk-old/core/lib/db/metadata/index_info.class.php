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
* Represents an index.
* 
*/
class index_info
{
	/**
	* * name of the index
	*/
	var $name;

	/**
	* * columns in this index
	*/
	var $columns = array();

	function index_info($name)
	{
		$this->name = $name;
	} 

	function get_name()
	{
		return $this->name;
	} 

	function add_column(&$column)
	{
		$this->columns[] = &$column;
	} 

	function get_columns()
	{
		return $this->columns;
	} 

	function to_string()
	{
		return $this->name;
	} 
} 
