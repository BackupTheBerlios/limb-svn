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

class strings_file_resolver extends package_file_resolver
{
  public function resolve($file_name, $locale_id)  
  {  
    if(!$resolved_path = parent :: resolve('i18n/' . $file_name . '_' . $locale_id . '.ini'))    
  	  throw new FileNotFoundException('strings file not found', $file_name, array('locale_id' => $locale_id));
  		  
		return $resolved_path;  
  }  
}

?>