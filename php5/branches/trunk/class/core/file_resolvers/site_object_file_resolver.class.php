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

class site_object_file_resolver extends package_file_resolver
{
  protected function _do_resolve($class_path)
  {
    if(file_exists(LIMB_DIR . 'class/core/site_objects/' . $class_path . '.class.php'))
      return LIMB_DIR . 'class/core/site_objects/' . $class_path . '.class.php';    
      
    if(!$resolved_path = $this->_find_file_in_packages('site_objects/' . $class_path . '.class.php'))    
  	  throw new FileNotFoundException('site object not found', $class_path);
  		  
		return $resolved_path;
  }  
}

?>