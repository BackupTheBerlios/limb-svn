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
require_once(LIMB_DIR . 'core/lib/system/objects_support.inc.php');

class site_object_factory
{
	function site_object_factory()
	{
	}
		
	function create($class_name)
	{	
  	return create_object($class_name, '/core/model/site_objects/');	
	}
	
	function & instance($class_name)
	{	
		$obj =&	instantiate_object($class_name, '/core/model/site_objects/');
		return $obj;
	}
	
}
?>