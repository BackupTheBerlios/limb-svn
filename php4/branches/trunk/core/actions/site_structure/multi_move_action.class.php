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
require_once(LIMB_DIR . '/core/actions/form_action.class.php');

class multi_move_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'grid_form';
  }

  function _init_validator()
  {
    $this->validator->add_rule($v = array(LIMB_DIR . '/core/lib/validators/rules/tree_node_id_rule', 'parent_node_id'));
  }

  function _init_dataspace(&$request)
  {
    parent :: _init_dataspace($request);

    $this->_transfer_dataspace($request);

    $this->_init_parent_node_id();
  }

  function _transfer_dataspace(&$request)
  {
    parent :: _transfer_dataspace($request);

    $this->_fill_template_grid();
  }

  function _init_parent_node_id()
  {
    if(!$ids = $this->dataspace->get('ids'))
      return;

    $id = key($ids);//we need only one

    $object = fetch_one_by_node_id($id);

    $this->dataspace->set('parent_node_id', $object['parent_node_id']);
  }

  function _fill_template_grid()
  {
    if(!$ids = $this->dataspace->get('ids', array()))
      return;

    $objects = $this->_get_objects_to_move(array_keys($ids));

    $grid =& $this->view->find_child('multi_move');

    $grid->register_dataset(new array_dataset($objects));

  }

  function validate()
  {
    if(!$result = parent :: validate())
      return $result;

    $ids = $this->dataspace->get('ids', array());
    $new_parent_node_id = $this->dataspace->get('parent_node_id');

    if(!sizeof($ids))
      return false;

    $objects = $this->_get_objects_to_move(array_keys($ids));

    $tree =& tree :: instance();

    foreach($objects as $id => $item)
    {
      $site_object =& wrap_with_site_object($item);

      if(!$tree->can_move_tree($site_object->get_node_id(), $new_parent_node_id))
      {
        $this->validator->add_error('parent_node_id', strings :: get('tree_move_recursion_error', 'error'));
        return false;
      }
    }

    return true;
  }

  function _valid_perform(&$request, &$response)
  {
    $ids = $this->dataspace->get('ids');
    $new_parent_node_id = $this->dataspace->get('parent_node_id');

    $objects = $this->_get_objects_to_move(array_keys($ids));

    $tree =& tree :: instance();

    foreach($objects as $id => $item)
    {
      $site_object =& wrap_with_site_object($item);

      $site_object->set_parent_node_id($new_parent_node_id);

      if(!$site_object->update(false))
      {
        debug :: write_error("object couldn't be moved",
         __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
        array('node_id' => $id));

        $request->set_status(REQUEST_STATUS_FAILURE);

        return;
      }
    }
    $request->set_status(REQUEST_STATUS_SUCCESS);

    $response->write(close_popup_response($request));
  }

  function _get_objects_to_move($node_ids)
  {
    $params = array(
      'restrict_by_class' => false
    );

    $objects = fetch_by_node_ids($node_ids, 'site_object', $counter, $params);

    foreach($objects as $id => $item)
    {
      $objects[$id]['ids'][$item['node_id']] = 1;
    }

    return $objects;
  }

}

?>