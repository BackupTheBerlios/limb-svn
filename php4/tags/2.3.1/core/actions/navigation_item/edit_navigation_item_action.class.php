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
require_once(LIMB_DIR . '/core/actions/form_edit_site_object_action.class.php');

class edit_navigation_item_action extends form_edit_site_object_action
{
  function _define_site_object_class_name()
  {
    return 'navigation_item';
  }

  function _define_dataspace_name()
  {
    return 'edit_navigation_item';
  }

  function _define_datamap()
  {
    return complex_array :: array_merge(
        parent :: _define_datamap(),
        array(
          'url' => 'url',
          'new_window' => 'new_window',
        )
    );
  }

  function _define_increase_version_flag()
  {
    return false;
  }


  function _init_validator()
  {
    parent :: _init_validator();

    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'title'));
    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'url'));
  }
}

?>