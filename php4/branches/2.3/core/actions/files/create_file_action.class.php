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
require_once(LIMB_DIR . '/core/actions/form_create_site_object_action.class.php');

class create_file_action extends form_create_site_object_action
{
  function _define_site_object_class_name()
  {
    return 'file_object';
  }

  function _define_dataspace_name()
  {
    return 'create_file';
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

  function _init_validator()
  {
    parent :: _init_validator();

    $v = array();

    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'title'));
    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/file_upload_required_rule', 'file'));
  }

  function _create_object_operation()
  {
    $file = $this->dataspace->get('file');

    $this->object->set_attribute('tmp_file_path', $file['tmp_name']);
    $this->object->set_attribute('file_name', $file['name']);
    $this->object->set_attribute('mime_type', $file['type']);

    return parent :: _create_object_operation();
  }

}

?>