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
require_once(LIMB_DIR . 'class/core/fetcher.class.php');

class fetch_component extends component
{
	protected $path = '';
			
	public function fetch($path=null)
	{
		if(!$path)
			$path = $this->get_path();
		
		$arr = LimbToolsBox :: getToolkit()->getFetcher()->fetch_one_by_path($path);
		$this->import($arr);
	}
		
	public function fetch_requested_object()
	{
    $request = LimbToolsBox :: getToolkit()->getRequest();
		$object_arr = LimbToolsBox :: getToolkit()->getFetcher()->fetch_requested_object($request);

		if ($version = $request->get('version'))
		{
			$site_object = LimbToolsBox :: getToolkit()->createSiteObject($object_arr['class_name']);
			$site_object->merge($object_arr);
			$object_arr = $site_object->fetch_version((int)$version);
		}
		
		$this->import($object_arr);
	}
	
	public function set_path($path)
	{
		$this->path = $path;
	}
	
	public function get_path()
	{
		if(!$this->path)
		{
      $request = LimbToolsBox :: getToolkit()->getRequest();
			$object_arr = LimbToolsBox :: getToolkit()->getFetcher()->fetch_requested_object($request);
			$this->path = $object_arr['path'];
		}	

		return $this->path;				
	}
		
} 

?>