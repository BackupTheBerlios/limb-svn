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
require_once(LIMB_DIR . 'class/lib/error/debug.class.php');
require_once(LIMB_DIR . 'class/core/actions/empty_action.class.php');

class action_factory
{	
	static protected function get_class_name($class_path)
	{
		$pos = strrpos($class_path, '/');
		
		if($pos !== false)
			$class_name = substr($class_path, $pos + 1);
		else
			$class_name = $class_path;
			
		return $class_name;
	}
		
	static public function create($class_path)
	{
		$class_name = self :: get_class_name($class_path);
		
		if(!class_exists($class_name))
		{
  		resolve_handle($resolver =& get_file_resolver('action'));
  		
  		try
  		{ 
  		  $full_path = $resolver->resolve($class_path);
  		}
  		catch(FileNotFoundException $e)
  		{
  		  debug :: write_exception($e);
  		  return new empty_action();
  		}
  			
  		include_once($full_path);
  	}
  	
	  return new $class_name();
	}	
}


?>