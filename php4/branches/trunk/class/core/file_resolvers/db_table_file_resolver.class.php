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

class db_table_file_resolver extends package_file_resolver
{
  function resolve($class_path)
  {
    if(!$resolved_path = parent :: resolve('db_tables/' . $class_path . '_db_table.class.php'))    
  	{
  	  debug :: write_error('db_table not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
  	    array('class_path' => $class_path));
  	    
  	  return false;
  	}
  		  
		return $resolved_path;
  }  
}

?>