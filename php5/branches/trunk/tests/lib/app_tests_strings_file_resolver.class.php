<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolver_decorator.class.php');

class app_tests_strings_file_resolver extends file_resolver_decorator
{  
  public function resolve($file_name, $params = array())
  {  
    if(!isset($params[0]))
      $locale_id = DEFAULT_CONTENT_LOCALE_ID;
    else
      $locale_id = $params[0];

  	if(file_exists(LIMB_DIR . '/tests/i18n/' . $file_name . '_' . $locale_id . '.ini'))
  	{
  		$dir = LIMB_DIR . '/tests/i18n/';
  		return $dir . $file_name . '_' . $locale_id . '.ini';
  	}	
       
    return $this->_resolver->resolve('i18n/' . $file_name . '_' . $locale_id . '.ini');
  }  
}

?>