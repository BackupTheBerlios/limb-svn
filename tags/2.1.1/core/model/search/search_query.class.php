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

class search_query
{
	var $items = array();
	
	function add($item)
	{
		$this->items[] = $item; 
	}
	
	function to_string()
	{
		return implode(' ', $this->items);
	}
	
	function get_query_items()
	{
		return $this->items;
	}
	
	function is_empty()
	{
		return (sizeof($this->items) == 0);
	}
}

?>