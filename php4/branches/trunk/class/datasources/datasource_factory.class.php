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

class datasource_factory
{
	function _extract_class_name($class_path)
	{
		$pos = strrpos($class_path, '/');
		
		if($pos !== false)
			return substr($class_path, $pos + 1);
		else
			return $class_path;	
	}
		
	function & create($class_path)
	{
		$class_name = datasource_factory :: _extract_class_name($class_path);

		if(!class_exists($class_name))
		{
  		$resolver =& get_file_resolver('datasource');
  		resolve_handle($resolver);
  		
  		if(!$full_path = $resolver->resolve($class_path))
  			return null;
  			
  		include_once($full_path);
  	}
		
	  $datasource =& new $class_name();
	  
	  return $datasource;
	}
}


?>