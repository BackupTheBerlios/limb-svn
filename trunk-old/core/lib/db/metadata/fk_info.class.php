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
* Represents a foreign key.
*/
class fk_info
{
	var $name;
	var $references = array();

	/**
	* 
	* @param string $name The name of the foreign key.
	*/
	function fk_info($name)
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
	* Adds a foreign-local mapping.
	* 
	* @param column_info $local 
	* @param column_info $foreign 
	*/
	function add_reference(&$local, &$foreign)
	{
		if (! is_a($local, 'column_info'))
		{
			debug :: write_warning("parameter 1 not of type 'column_info"',
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		} 
		elseif (! is_a($foreign, 'column_info'))
		{
			debug :: write_warning("parameter 2 not of type 'column_info"',
			 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
		} 
		$ref[] = &$local;
		$ref[] = &$foreign;
		$this->references[] = &$ref;
	} 

	/**
	* Gets the local-foreign column mapping.
	* 
	* @return array array( [0] => array([0] => local column_info object, [1] => foreign column_info object) )
	*/
	function &get_references()
	{
		return $this->references;
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
