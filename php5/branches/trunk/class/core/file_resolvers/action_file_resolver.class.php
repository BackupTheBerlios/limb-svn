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

class action_file_resolver extends package_file_resolver
{
  protected function _do_resolve($class_path)
  {
   if(file_exists(LIMB_DIR . 'class/core/actions/' . $class_path . '.class.php'))
      return LIMB_DIR . 'class/core/actions/' . $class_path . '.class.php'; 
        
    if(!$resolved_path = $this->_find_file_in_packages('actions/' . $class_path . '.class.php'))    
  	  throw new FileNotFoundException('action not found', $class_path);
  		  
		return $resolved_path;
  }  
}

?>