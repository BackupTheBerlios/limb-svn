<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'class/core/actions/form_site_object_action.class.php');
require_once(LIMB_DIR . 'class/core/fetcher.class.php');

abstract class form_create_site_object_action extends form_site_object_action
{
  protected function _init_dataspace($request)
  {
    parent :: _init_dataspace($request);

    if (($parent_node_id = $this->dataspace->get('parent_node_id')) === null)
    {
      $parent_object_data = $this->_load_parent_object_data($request);
      $this->dataspace->set('parent_node_id', $parent_object_data['node_id']);
    }
  }

	protected function _init_validator()
	{
		if (($parent_node_id = $this->dataspace->get('parent_node_id')) === null)
		{
		  if(!$parent_object_data = $this->_load_parent_object_data())
		  	return;

			$parent_node_id = $parent_object_data['parent_node_id'];
		}

		$this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/tree_node_id_rule', 'parent_node_id'));

		if($this->object->is_auto_identifier())
			return;

		$this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'identifier'));
		$this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/tree_identifier_rule', 'identifier', $parent_node_id));
	}

	protected function _valid_perform($request, $response)
	{
		$parent_object_data = $this->_load_parent_object_data();

		$data['parent_node_id'] = $parent_object_data['node_id'];

		$this->_valid_perform_prepare_data($data);

		$this->object->merge($data);

		if($this->_create_object_operation() === false)
		{
		  $request->set_status(request :: STATUS_FAILURE);
			return;
		}

		$this->indexer->add($this->object);

		$this->_write_create_access_policy();

		$request->set_status(request :: STATUS_FORM_SUBMITTED);

		if($request->has_attribute('popup'))
			$response->write(close_popup_response($request));
	}

	protected function _create_object_operation()
	{
		if(!$object_id = $this->object->create())
			return false;

		return $object_id;
	}

	protected function _write_create_access_policy()
	{
		$parent_data = $this->_load_parent_object_data();

		$parent_object = Limb :: toolkit()->createSiteObject($parent_data['class_name']);

		$parent_object->merge($parent_data);

		$action = $parent_object->get_controller()->determine_action();

    $access_policy = new access_policy();
		$access_policy->save_new_object_access($this->object, $parent_object, $action);
	}

	protected function _valid_perform_prepare_data(&$data)
	{
		complex_array :: map($this->datamap, $this->dataspace->export(), $data);
	}

	protected function _load_parent_object_data($request)
	{
		return Limb :: toolkit()->getFetcher()->fetch_requested_object($request);
	}
}
?>