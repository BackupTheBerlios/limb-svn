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
require_once(LIMB_DIR . 'core/actions/form_create_site_object_action.class.php');

class create_navigation_item_action extends form_create_site_object_action
{
  function _define_controller_class_name()
  {
    return 'navigation_item_controller';
  }

  function _define_site_object_class_name()
  {
    return 'navigation_item';
  }

  function _define_dataspace_name()
  {
    return 'create_navigation_item';
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

    $this->validator->add_rule($v[] = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'title'));
    $this->validator->add_rule($v[] = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'url'));
  }
}

?>