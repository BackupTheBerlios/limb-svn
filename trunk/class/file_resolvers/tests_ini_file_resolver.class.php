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

class tests_ini_file_resolver
{
  function resolve($file_name)
  {
  	if (file_exists(LIMB_DIR . 'tests/settings/' . $file_name))
  		$dir = LIMB_DIR . 'tests/settings/';
  	elseif (file_exists(LIMB_DIR . 'core/settings/' . $file_name))
  		$dir = LIMB_DIR . 'core/settings/';
  	else
  		error('ini file not found', 
		  __FILE__ . ' : ' . __LINE__ . ' : ' . __FUNCTION__, 
		  array('file' => $file_name));
		  
		return $dir . $file_name;
  }
}

?>