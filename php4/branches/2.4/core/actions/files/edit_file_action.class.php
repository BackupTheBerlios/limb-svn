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

class edit_file_action extends form_edit_site_object_action
{
  function _define_site_object_class_name()
  {
    return 'file_object';
  }

  function _define_dataspace_name()
  {
    return 'edit_file';
  }

  function _define_datamap()
  {
    return complex_array :: array_merge(
        parent :: _define_datamap(),
        array(
          'description' => 'description',
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

    $this->validator->add_rule($v = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'title'));
  }

  function _update_object_operation()
  {
    if($file = $this->dataspace->get('file'))
    {
      $object_data =& fetch_requested_object();

      $this->object->set_attribute('media_id', $object_data['media_id']);
      $this->object->set_attribute('tmp_file_path', $file['tmp_name']);
      $this->object->set_attribute('file_name', $file['name']);
      $this->object->set_attribute('mime_type', $file['type']);
    }

    return parent :: _update_object_operation();
  }
}

?>