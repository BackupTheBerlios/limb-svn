<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: available_locales_datasource.class.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/datasource/object_detail_info_datasource.class.php');
require_once(LIMB_DIR . '/core/lib/i18n/locale.class.php');

class available_content_locales_datasource extends object_detail_info_datasource
{
  function get_options_array()
  {
    return locale :: get_available_locales_data();
  }

  function get_default_option()
  {
    $object_data = $this->_fetch_object_data();
    return $object_data['locale_id'];
  }
}
?>