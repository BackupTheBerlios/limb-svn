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

class tests_action_file_resolver
{
  function resolve($class_path)
  {
		if(file_exists(LIMB_DIR . '/class/core/actions/' . $class_path . '.class.php'))
			$full_path = LIMB_DIR . '/class/core/actions/' . $class_path . '.class.php';
  	else
      throw new FileNotFoundException('action not found', $class_path);
  		  
		return $full_path;
  }  
}

?>