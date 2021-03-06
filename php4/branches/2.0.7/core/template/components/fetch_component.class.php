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


require_once(LIMB_DIR . 'core/fetcher.class.php');

class fetch_component extends component
{
	var $path = '';
			
	function fetch($path=null)
	{
		if(!$path)
			$path = $this->get_path();
		
		$arr = fetch_one_by_path($path);
		$this->import($arr);
	}
		
	function fetch_mapped_by_url()
	{
		$object_arr = fetch_mapped_by_url();
		
		$this->import($object_arr);
	}
	
	function set_path($path)
	{
		$this->path = $path;
	}
	
	function get_path()
	{
		if(!$this->path)
			$this->path = $_SERVER['PHP_SELF'];

		return $this->path;				
	}
		
} 

?>