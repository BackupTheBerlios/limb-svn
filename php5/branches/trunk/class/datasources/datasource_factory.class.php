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

if(!is_registered_resolver('datasource'))
{
  include_once(LIMB_DIR . '/class/core/file_resolvers/package_file_resolver.class.php');
  include_once(LIMB_DIR . '/class/core/file_resolvers/datasource_file_resolver.class.php');
  register_file_resolver('datasource', new datasource_file_resolver(new package_file_resolver()));
}

class datasource_factory
{
  protected function __construct(){}
  
	static protected function _extract_class_name($class_path)
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
  		resolve_handle($resolver =& get_file_resolver('datasource'));
  		
  		if(!$full_path = $resolver->resolve($class_path))
  			return null;
  			
  		include_once($full_path);
  	}
		
	  $datasource = new $class_name();
	  
	  return $datasource;
	}
}


?>