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
require_once(LIMB_DIR . 'class/lib/system/objects_support.inc.php');

if(!is_registered_resolver('site_object'))
  register_file_resolver('site_object', $r = LIMB_DIR . '/class/core/file_resolvers/site_object_file_resolver');

abstract class site_object_factory
{
	static function create($class_name)
	{	
	  self :: _include_class_file($class_name);
	  
  	return new $class_name();	
	}
	
	static protected function _include_class_file($class_name)
	{
	  if(class_exists($class_name))
	    return;
	
		resolve_handle($resolver =& get_file_resolver('site_object'));
		
		$full_path = $resolver->resolve($class_name);

		include_once($full_path);
	}
}
?>