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

class strings_file_resolver
{
  function resolve($file_name, $locale_id)
  {  
		if(file_exists(LIMB_APP_DIR . '/class/i18n/strings/' . $file_name . '_' . $locale_id . '.ini'))
  		$dir = LIMB_APP_DIR . '/class/i18n/strings/';
  	elseif(file_exists(LIMB_DIR . '/class/i18n/strings/' . $file_name . '_' . $locale_id . '.ini'))
  		$dir = LIMB_DIR . '/class/i18n/strings/';
  	else
  		error('strings file not found', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
  			array(
  				'file_name' => $file_name,
  				'locale_id' => $locale_id
  			)
  	);
  	
  	return $dir . $file_name . '_' . $locale_id . '.ini';
  }  
}

?>