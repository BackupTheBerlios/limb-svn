<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/file_resolvers/file_resolver.interface.php');

class tests_strings_file_resolver implements file_resolver
{
  function resolve($file_name, $params = array())
  {
    if(!isset($params[0]))
      $locale_id = DEFAULT_CONTENT_LOCALE_ID;
    else
      $locale_id = $params[0];

    if(file_exists(LIMB_DIR . '/tests/i18n/' . $file_name . '_' . $locale_id . '.ini'))
      $dir = LIMB_DIR . '/tests/i18n/';
    else
      throw new FileNotFoundException('strings file not found', $file_name, array('locale_id' => $locale_id));

    return $dir . $file_name . '_' . $locale_id . '.ini';
  }
}

?>