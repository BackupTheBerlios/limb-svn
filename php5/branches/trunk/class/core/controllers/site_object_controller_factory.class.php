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

abstract class site_object_controller_factory
{	
	static function create($class_name)
	{
	  self :: _include_class_file($class_name);
	  
  	return new $class_name();
	}
	
	static private function _include_class_file($class_name)
	{
	  if(class_exists($class_name))
	    return;
	
		$resolver = get_file_resolver('controller');
		resolve_handle($resolver);
		
		$full_path = $resolver->resolve($class_name);

		include_once($full_path);
	}
}


?>