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

class tests_common_file_resolver
{
  function resolve($class_path, $relevant_dir)
  {
		if(file_exists(LIMB_DIR . $relevant_dir . $class_path . '.class.php'))
			$full_path = LIMB_DIR . $relevant_dir . $class_path . '.class.php';
  	else
  	{
  	  debug :: write_error('file not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
  	    array('class_path' => $class_path));
  	    
  	  return false;
  	}
  		  
		return $full_path;
  }  
}

?>