<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/model/site_objects/content_object.class.php');

class main_page extends content_object
{
  function _define_class_properties()
  {
    return array(
      'class_ordr' => 0,
      'can_be_parent' => 1,
      'icon' => '/shared/images/folder.gif',
      'db_table_name' => 'document'
    );
  }
}

?>