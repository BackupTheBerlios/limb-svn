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
require_once(LIMB_DIR . '/core/actions/form_action.class.php');

class register_new_object_action extends form_action
{
  function _define_dataspace_name()
  {
    return 'register_new_object';
  }

  function _init_dataspace(&$request)
  {
    parent :: _init_dataspace($request);

    $root = fetch_one_by_path('/root');

    $this->dataspace->set('parent_node_id', $root['node_id']);
  }

  function _init_validator()
  {
    $v = array();

    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'class_name'));
    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/invalid_value_rule', 'class_name', 0));
    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/invalid_value_rule', 'class_name', -1));

    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'controller_name'));
    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/invalid_value_rule', 'controller_name', 0));
    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/invalid_value_rule', 'controller_name', -1));

    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'identifier'));

    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/tree_node_id_rule', 'parent_node_id'));
    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'parent_node_id'));

    $this->validator->add_rule($v[] = array(LIMB_DIR . '/core/lib/validators/rules/required_rule', 'title'));
  }

  function _valid_perform(&$request, &$response)
  {
    $tree =& tree :: instance();

    $params = array();

    $params['identifier'] = $this->dataspace->get('identifier');
    $params['class'] = $this->dataspace->get('class_name');
    $params['title'] = $this->dataspace->get('title');
    $params['parent_node_id'] = $this->dataspace->get('parent_node_id');
    $params['controller_id'] = site_object_controller :: get_id($this->dataspace->get('controller_name'));

    $object =& site_object_factory :: create($params['class']);

    if(!$parent_data = fetch_one_by_node_id($params['parent_node_id']))
    {
       error("parent wasn't retrieved",
        __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
    }

    $object->merge_attributes($params);

    if(!$object->create())
    {
      error("object wasn't registered",
       __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
    }

    $parent_object =& site_object_factory :: create($parent_data['class_name']);
    $parent_object->merge_attributes($parent_data);

    $access_policy =& access_policy :: instance();
    $access_policy->save_initial_object_access($object, $parent_object);

    $request->set_status(REQUEST_STATUS_FORM_SUBMITTED);

    if($request->has_attribute('popup'))
      $response->write(close_popup_response($request));
  }
}

?>