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

class ini_file_resolver
{
  public function resolve($file_name)
  {
  	if (file_exists(LIMB_APP_DIR . '/settings/' . $file_name))
  		$dir = LIMB_APP_DIR . '/settings/';
  	elseif (file_exists(LIMB_DIR . '/settings/' . $file_name))
  		$dir = LIMB_DIR . '/settings/';
  	else
  		throw new FileNotFoundException('ini file not found', $file_name);
		  
		return $dir . $file_name;
  }  
}

?>