<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 

class search_query
{
	protected $items = array();
	
	public function add($item)
	{
		$this->items[] = $item; 
	}
	
	public function to_string()
	{
		return implode(' ', $this->items);
	}
	
	public function get_query_items()
	{
		return $this->items;
	}
	
	public function is_empty()
	{
		return (sizeof($this->items) == 0);
	}
}

?>