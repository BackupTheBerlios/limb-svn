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
require_once(LIMB_DIR . 'class/lib/error/debug.class.php');

if(!is_registered_resolver('datasource'))
  register_file_resolver('datasource', $r = LIMB_DIR . '/class/core/file_resolvers/datasource_file_resolver');

class datasource_factory
{
  private function __construct(){}
  
	static private function _extract_class_name($class_path)
	{
		$pos = strrpos($class_path, '/');
		
		if($pos !== false)
			return substr($class_path, $pos + 1);
		else
			return $class_path;	
	}
		
	static public function create($class_path)
	{
		$class_name = self :: _extract_class_name($class_path);

		if(!class_exists($class_name))
		{
  		$resolver =& get_file_resolver('datasource');
  		resolve_handle($resolver);
  		
  		if(!$full_path = $resolver->resolve($class_path))
  			return null;
  			
  		include_once($full_path);
  	}
		
	  $datasource = new $class_name();
	  
	  return $datasource;
	}
}


?>