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


require_once(LIMB_DIR . 'class/core/fetcher.class.php');

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
		
	function fetch_requested_object()
	{
		$object_arr = fetch_requested_object();
		
		$request = request :: instance();
		
		if ($version = $request->get('version'))
		{
			$site_object = site_object_factory :: create($object_arr['class_name']);
			$site_object->merge($object_arr);
			$object_arr = $site_object->fetch_version((int)$version);
		}
		
		$this->import($object_arr);
	}
	
	function set_path($path)
	{
		$this->path = $path;
	}
	
	function get_path()
	{
		if(!$this->path)
		{
			$object_arr =& fetch_requested_object();
			$this->path = $object_arr['path'];
		}	

		return $this->path;				
	}
		
} 

?>