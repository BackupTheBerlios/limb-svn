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
require_once(LIMB_DIR . 'core/actions/form_site_object_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/tree_identifier_rule.class.php');
require_once(LIMB_DIR . 'core/model/search/full_text_indexer.class.php');
require_once(LIMB_DIR . 'core/fetcher.class.php');

class form_edit_site_object_action extends form_site_object_action
{
	var $definition = array(
		'site_object' => 'site_object',
		'datamap' => array(
			'parent_node_id' => 'parent_node_id',
			'identifier' => 'identifier',
			'title' => 'title'
		)
	);
	
	var $indexer = null;	
	
	function form_edit_site_object_action($name='', $merge_definition=array())
	{		
		$this->indexer =& $this->_get_site_object_indexer();
		
		parent :: form_site_object_action($name ,$merge_definition);
	}
	
	function & _get_site_object_indexer()
	{
		return new full_text_indexer();
	}
	
	function _init_validator()
	{
		if($this->object->is_auto_identifier())
			return;
			
		$this->validator->add_rule(new required_rule('identifier'));
		
		if(!$object_data = fetch_mapped_by_url())
			return;

		if(($parent_node_id = $this->dataspace->get('parent_node_id')) === null)
			$parent_node_id = $object_data['parent_node_id'];
		
		$this->validator->add_rule(new tree_identifier_rule('identifier', (int)$parent_node_id, (int)$object_data['node_id']));
	}

	function _init_dataspace()
	{
		$object_data =& fetch_mapped_by_url();

		$data = array();
		complex_array :: map(array_flip($this->definition['datamap']), $object_data, $data);
		
		$this->dataspace->import($data);
	}

	function _valid_perform()
	{
		$object_data =& $this->_load_object_data();

		$data_to_import['id'] = $object_data['id'];
		$data_to_import['node_id'] = $object_data['node_id'];
		$data_to_import['parent_node_id'] = $object_data['parent_node_id'];
		$data_to_import['identifier'] = $object_data['identifier'];
		$data_to_import['title'] = $object_data['title'];
		
		complex_array :: map($this->definition['datamap'], $this->dataspace->export(), $data_to_import);
		
		if (!isset($data_to_import['status']))
			$data_to_import['status'] = $object_data['status'];
			
		$this->object->import_attributes($data_to_import);
		
		if(!$this->_update_object_operation())
			return new failed_response();

		$this->indexer->add($this->object);
		
		if(isset($data_to_import['identifier']) && $object_data['identifier'] != $data_to_import['identifier'])
		{
			$this->_handle_changed_identifier($data_to_import['identifier']);
		}	
			
		return new response(RESPONSE_STATUS_FORM_SUBMITTED);
	}
	
	function _update_object_operation()
	{
		if(!$this->object->update())
			return false;
		else
			return true;
	}
	
	function _handle_changed_identifier($new_identifier)
	{
	}
	
	function & _load_object_data()
	{
		$result =& fetch_mapped_by_url();
		return $result;
	}
}
?>