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

class form_create_site_object_action extends form_site_object_action
{
	var $definition = array(
		'site_object' => 'site_object',
		'datamap' => array(
			'identifier' => 'identifier',
			'title' => 'title'
		)
	);
	
	var $indexer = null;
	
	function form_create_site_object_action($name='', $merge_definition=array())
	{		
		$this->indexer =& $this->_get_site_object_indexer();
		
		parent :: form_site_object_action($name, $merge_definition);
	}
	
	function & _get_site_object_indexer()
	{
		return new full_text_indexer();
	}
	
	function _init_validator()
	{
		$parent_object_data =& $this->_load_parent_object_data();
		
		if(!$this->object->is_auto_identifier())
		{
			$this->validator->add_rule(new required_rule('identifier'));
			
			if($parent_object_data)
				$this->validator->add_rule(new tree_identifier_rule('identifier', $parent_object_data['node_id']));
		}
	}
	
	function _valid_perform()
	{
		$parent_object_data =& $this->_load_parent_object_data();
		
		$data['parent_id'] = $parent_object_data['node_id'];
		
		$this->_valid_perform_prepare_data($data);
		
		$this->object->import_attributes($data);

		if(($object_id = $this->_create_object_operation()) === false)
			return false;
			
		$this->indexer->add($this->object);
		
		$this->_write_create_access_policy();
		
		if(!isset($_REQUEST['popup']) || !$_REQUEST['popup'])
			return $object_id;
			
		close_popup();
	}
	
	function _create_object_operation()
	{
		if(!$object_id = $this->object->create())
			return false;

		return $object_id;
	}
	
	function _write_create_access_policy()
	{
		$parent_data =& $this->_load_parent_object_data();

		$parent_object =& site_object_factory :: instance($parent_data['class_name']);
		
		$parent_object->import_attributes($parent_data);

		$access_policy =& access_policy :: instance();
		
		$access_policy->save_object_access($this->object, $parent_object);
	}
	
	function _valid_perform_prepare_data(&$data)
	{
		complex_array :: map($this->definition['datamap'], $this->dataspace->export(), $data);
	}

	function & _load_parent_object_data()
	{
		$result =& fetch_mapped_by_url();
		return $result;
	}
}
?>