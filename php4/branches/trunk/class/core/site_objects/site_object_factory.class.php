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

class site_object_factory
{
	function create($class_name)
	{	
	  site_object_factory :: _include_class_file($class_name);
	  
  	return create_object($class_name);	
	}
	
	function & instance($class_name)
	{	
	  site_object_factory :: _include_class_file($class_name);
	  
		$obj =&	instantiate_object($class_name);
		return $obj;
	}

	function _include_class_file($class_name)
	{
	  if(class_exists($class_name))
	    return;
	
		$resolver =& get_file_resolver('site_object');
		resolve_handle($resolver);
		
		$full_path = $resolver->resolve($class_name);

		include_once($full_path);
	}
}
?>