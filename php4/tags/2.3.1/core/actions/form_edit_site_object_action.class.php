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
require_once(LIMB_DIR . '/core/actions/form_site_object_action.class.php');
require_once(LIMB_DIR . '/core/fetcher.class.php');

class form_edit_site_object_action extends form_site_object_action
{
  var $_increase_version;

  function form_edit_site_object_action()
  {
    parent :: form_site_object_action();

    $this->_increase_version = $this->_define_increase_version_flag();
  }

  function _define_increase_version_flag()
  {
    if (is_a($this->object, 'content_object'))
      return true;
    else
      return false;
  }

  function _init_validator()
  {
    if(!$object_data = $this->_load_object_data())
      return;

    if($this->object->is_auto_identifier())
      return;

    $v = array();
    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/tree_node_id_rule', 'parent_node_id'));
    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'identifier'));

    if(($parent_node_id = $this->dataspace->get('parent_node_id')) === null)
      $parent_node_id = $object_data['parent_node_id'];

    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/tree_identifier_rule', 'identifier', (int)$parent_node_id, (int)$object_data['node_id']));
  }

  function _init_dataspace(&$request)
  {
    $object_data =& $this->_load_object_data();

    $data = array();
    complex_array :: map(array_flip($this->datamap), $object_data, $data);

    $this->dataspace->import($data);
  }

  function _valid_perform(&$request, &$response)
  {
    $object_data =& $this->_load_object_data();

    $this->_valid_perform_prepare_data($object_data);

    $this->object->merge_attributes($object_data);

    if(!$this->_update_object_operation())
    {
      $request->set_status(REQUEST_STATUS_FAILURE);
      return;
    }

    $this->indexer->add($this->object);

    if(isset($data_to_import['identifier']) && $object_data['identifier'] != $data_to_import['identifier'])
    {
      $this->_handle_changed_identifier($data_to_import['identifier']);
    }

    $request->set_status(REQUEST_STATUS_FORM_SUBMITTED);

    flush_fetcher_cache();
  }

  function _update_object_operation()
  {
    if ($this->dataspace->get('minor_changes') || ($this->_increase_version == false))
      $result = $this->object->update(false);
    else
      $result = $this->object->update(true);

    return ($result !== false) ? true : false;
  }

  function _valid_perform_prepare_data(&$data)
  {
    complex_array :: map($this->datamap, $this->dataspace->export(), $data);
  }

  function _handle_changed_identifier($new_identifier)
  {
  }

  function & _load_object_data()
  {
    $result =& fetch_requested_object();
    return $result;
  }
}
?>