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

class tests_strings_file_resolver
{
  function resolve($file_name, $locale_id)
  { 
  	if(file_exists(LIMB_DIR . '/tests/i18n/' . $file_name . '_' . $locale_id . '.ini'))
  		$dir = LIMB_DIR . '/tests/i18n/';
  	else
      throw new FileNotFoundException('strings file not found', $file_name, array('locale_id' => $locale_id));
  	
  	return $dir . $file_name . '_' . $locale_id . '.ini';
  }  
}

?>