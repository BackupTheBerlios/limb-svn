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

class poll_answer extends content_object
{
  function _define_attributes_definition()
  {
    return complex_array :: array_merge(
        parent :: _define_attributes_definition(),
        array(
        'identifier' => array('search' => true)
        ));
  }

  function _define_class_properties()
  {
    return array(
      'class_ordr' => 2,
      'can_be_parent' => 0,
      'icon' => '/shared/images/folder.gif',
      'auto_identifier' => true
    );
  }
}

?>