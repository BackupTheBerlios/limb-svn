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

class register_new_object_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'register_new_object';
  }

  function _init_validator()
  {
    $this->validator->add_rule($v[] = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'class_name'));
    $this->validator->add_rule($v[] = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'controller_name'));
    $this->validator->add_rule($v[] = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'identifier'));
    $this->validator->add_rule($v[] = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'parent_path'));
    $this->validator->add_rule($v[] = array(LIMB_DIR . 'core/lib/validators/rules/tree_path_rule', 'parent_path'));

    if($path = $this->dataspace->get('parent_path'))
    {
      $tree = tree :: instance();
      if($node = $tree->get_node_by_path($path))
        $this->validator->add_rule($v[] = array(LIMB_DIR . 'core/lib/validators/rules/tree_identifier_rule', 'identifier', $node['id']));
    }

    $this->validator->add_rule($v[] = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'title'));
  }

  function _valid_perform(&$request, &$response)
  {
    $tree =& tree :: instance();

    $params = array();

    $params['identifier'] = $this->dataspace->get('identifier');
    $params['parent_path'] = $this->dataspace->get('parent_path');
    $params['class'] = $this->dataspace->get('class_name');
    $params['title'] = $this->dataspace->get('title');

    $params['controller_id'] = site_object_controller :: get_id($this->dataspace->get('controller_name'));

    $object =& site_object_factory :: create($params['class']);

    $is_root = false;
    if(!$parent_data = fetch_one_by_path($params['parent_path']))
    {
      if ($params['parent_path'] == '/')
        $is_root = true;
      else
       error("parent wasn't retrieved",
        __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
    }

    if (!$is_root)
      $params['parent_node_id'] = $parent_data['node_id'];
    else
      $params['parent_node_id'] = 0;

    $object->import_attributes($params);

    if(!$object->create($is_root))
    {
      error("object wasn't registered",
       __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
    }

    if (!$is_root)
    {
      $parent_object =& site_object_factory :: create($parent_data['class_name']);
      $parent_object->import_attributes($parent_data);

      $access_policy =& access_policy :: instance();
      $access_policy->save_object_access($object, $parent_object);
    }

    $request->set_status(REQUEST_STATUS_FORM_SUBMITTED);

    if($request->has_attribute('popup'))
      $response->write(close_popup_response($request));
  }
}

?>