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
require_once(LIMB_DIR . '/class/core/file_resolvers/package_file_resolver.class.php');

class controller_file_resolver extends package_file_resolver
{
  public function resolve($class_path)
  {
    if(!$resolved_path = parent :: resolve('controllers/' . $class_path . '.class.php'))    
  	  throw new FileNotFoundException('controller not found', $class_path);
  		  
		return $resolved_path;
  }  
}

?>