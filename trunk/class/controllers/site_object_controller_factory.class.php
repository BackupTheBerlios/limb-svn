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
require_once(LIMB_DIR . 'core/lib/error/debug.class.php');
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');

class site_object_controller_factory
{	
	function & create($class_name)
	{
	  site_object_controller_factory :: _include_class_file($class_name);
	  
  	return create_object($class_name);	
	}

	function instance($class_name)
	{
	  site_object_controller_factory :: _include_class_file($class_name);

		$obj =&	instantiate_object($class_name);
		return $obj;
	}		
	
	function _include_class_file($class_name)
	{
	  if(class_exists($class_name))
	    return;
	
		$resolver =& get_file_resolver('common');
		resolve_handle($resolver);
		
		$full_path = $resolver->resolve($class_name, '/class/controllers');

		include_once($full_path);
	}
}


?>