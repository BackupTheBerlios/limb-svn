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

abstract class site_object_behaviour_factory
{	
  static protected $_behaviours = array();
  
	static public function create($class_name)
	{
	  if(isset(self :: $_behaviours[$class_name]))
	    return self :: $_behaviours[$class_name];
	  
	  self :: _include_class_file($class_name);
	  
	  self :: $_behaviours[$class_name] = new $class_name();
  	return self :: $_behaviours[$class_name];
	}
	
	static protected function _include_class_file($class_name)
	{
	  if(class_exists($class_name))
	    return;
	
		resolve_handle($resolver =& get_file_resolver('behaviour'));
		
		$full_path = $resolver->resolve($class_name);

		include_once($full_path);
	}
}


?>