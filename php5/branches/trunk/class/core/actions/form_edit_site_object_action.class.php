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

abstract class form_edit_site_object_action extends form_site_object_action
{
  protected $_increase_version;

  function __construct()
  {
    parent :: __construct();

    $this->_increase_version = $this->_define_increase_version_flag();
  }

  protected function _define_increase_version_flag()
  {
    if (class_exists('content_object') && $this->object instanceof content_object)
      return true;
    else
      return false;
  }

	protected function _init_validator()
	{
		if(!$object_data = Limb :: toolkit()->getFetcher()->fetch_requested_object(Limb :: toolkit()->getRequest()))
			return;

		if($this->object->is_auto_identifier())
			return;

		$this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/tree_node_id_rule', 'parent_node_id'));
		$this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/required_rule', 'identifier'));

		if(($parent_node_id = $this->dataspace->get('parent_node_id')) === null)
			$parent_node_id = $object_data['parent_node_id'];

		$this->validator->add_rule(array(LIMB_DIR . 'class/validators/rules/tree_identifier_rule', 'identifier', (int)$parent_node_id, (int)$object_data['node_id']));
	}

	protected function _init_dataspace($request)
	{
		$object_data = $this->_load_object_data($request);

		$data = array();
		complex_array :: map(array_flip($this->datamap), $object_data, $data);

		$this->dataspace->import($data);
	}

	protected function _valid_perform($request, $response)
	{
		$object_data = $this->_load_object_data($request);

		$data_to_import['id'] = $object_data['id'];
		$data_to_import['node_id'] = $object_data['node_id'];
		$data_to_import['parent_node_id'] = $object_data['parent_node_id'];
		$data_to_import['identifier'] = $object_data['identifier'];
		$data_to_import['title'] = $object_data['title'];

		$this->_valid_perform_prepare_data($data_to_import);

		if (!isset($data_to_import['status']))
			$data_to_import['status'] = $object_data['status'];

		$this->object->merge($data_to_import);

		$this->_update_object_operation();

		$this->indexer->add($this->object);

		if(isset($data_to_import['identifier']) && $object_data['identifier'] != $data_to_import['identifier'])
		{
			$this->_handle_changed_identifier($data_to_import['identifier']);
		}

	  $request->set_status(request :: STATUS_FORM_SUBMITTED);

	  Limb :: toolkit()->getFetcher()->flush_cache();
	}

	protected function _update_object_operation()
	{
	  if ($this->dataspace->get('minor_changes') || ($this->_increase_version == false))
		  $this->object->update(false);
		else
		  $this->object->update(true);
	}

	protected function _valid_perform_prepare_data(&$data)
	{
		complex_array :: map($this->datamap, $this->dataspace->export(), $data);
	}

	protected function _handle_changed_identifier($new_identifier)
	{
	}

	protected function _load_object_data($request)
	{
		return Limb :: toolkit()->getFetcher()->fetch_requested_object($request);
	}
}
?>