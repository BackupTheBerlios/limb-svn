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

class app_tests_ini_file_resolver
{
  function resolve($file_name)
  {
  	if (file_exists(LIMB_APP_DIR . '/tests/settings/' . $file_name))
  		$dir = LIMB_APP_DIR . '/tests/settings/';
  	elseif(file_exists(LIMB_APP_DIR . '/settings/' . $file_name))
  	  $dir = LIMB_APP_DIR . '/settings/';
  	elseif (file_exists(LIMB_DIR . '/tests/settings/' . $file_name))
  		$dir = LIMB_DIR . '/tests/settings/';
  	elseif (file_exists(LIMB_DIR . '/settings/' . $file_name))
  		$dir = LIMB_DIR . '/settings/';
  	else
      throw new FileNotFoundException('ini file not found', $file_name);
		  
		return $dir . $file_name;
  }  
}

?>