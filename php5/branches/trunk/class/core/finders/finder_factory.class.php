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
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');

if(!is_registered_resolver('finder'))
{
  include_once(LIMB_DIR . '/class/core/file_resolvers/package_file_resolver.class.php');
  include_once(LIMB_DIR . '/class/core/file_resolvers/finder_file_resolver.class.php');
  register_file_resolver('finder', new finder_file_resolver(new package_file_resolver()));
}

abstract class finder_factory
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
	
		resolve_handle($resolver =& get_file_resolver('finder'));
		
		$full_path = $resolver->resolve($class_name);

		include_once($full_path);
	}
}
?>