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
require_once(LIMB_DIR . 'core/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/tree_identifier_rule.class.php');
require_once(LIMB_DIR . 'core/model/search/full_text_indexer.class.php');

class form_edit_site_object_action extends form_action
{
	var $definition = array(
		'site_object' => 'site_object',
		'datamap' => array(
			'identifier' => 'identifier',
			'title' => 'title'
		)
	);
	
	var $indexer = null;	
	
	function form_edit_site_object_action($name='', $merge_definition=array())
	{
		$this->definition = complex_array :: array_merge($this->definition, $merge_definition);
		
		$this->indexer =& $this->_get_site_object_indexer();
		
		parent :: form_action($name);
	}
	
	function & _get_site_object_indexer()
	{
		return new full_text_indexer();
	}
	
	function _init_validator()
	{
		$object_data =& fetch_mapped_by_url();
		
		$this->validator->add_rule(new required_rule('identifier'));
		$this->validator->add_rule(new tree_identifier_rule('identifier', $object_data['parent_id'], $object_data['identifier']));
	}

	function _init_dataspace()
	{
		$object_data =& fetch_mapped_by_url();

		$data = array();
		
		complex_array :: map(array_flip($this->definition['datamap']), $object_data, $data);
		
		$this->_import($data);
	}

	function _valid_perform()
	{
		$object_data =& $this->_load_object_data();

		$data['id'] = $object_data['id'];
		$data['node_id'] = $object_data['node_id'];
		$data['identifier'] = $object_data['identifier'];
		$data['title'] = $object_data['title'];
		
		complex_array :: map($this->definition['datamap'], $this->dataspace->export(), $data);
		
		$object =& site_object_factory :: create($this->definition['site_object']);
		
		if (!isset($data['status']))
			$data['status'] = $object_data['status'];
			
		$object->import_attributes($data);
		
		if(!$this->_update_object_operation($object))
			return false;

		$this->indexer->add($object);
		
		if(isset($data['identifier']) && $object_data['identifier'] != $data['identifier'])
		{
			$this->_handle_changed_identifier($data['identifier']);
		}	
			
		return true;
	}
	
	function _update_object_operation(&$object)
	{
		if(!$object->update())
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