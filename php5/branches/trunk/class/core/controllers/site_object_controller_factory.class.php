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
  static protected $_controllers = array();
  
	static function create($class_name)
	{
	  if(isset(self :: $_controllers[$class_name]))
	    return self :: $_controllers[$class_name];
	  
	  self :: _include_class_file($class_name);
	  
	  self :: $_controllers[$class_name] = new $class_name();
  	return self :: $_controllers[$class_name];
	}
	
	static protected function _include_class_file($class_name)
	{
	  if(class_exists($class_name))
	    return;
	
		resolve_handle($resolver =& get_file_resolver('controller'));
		
		$full_path = $resolver->resolve($class_name);

		include_once($full_path);
	}
}


?>