<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: form_create_site_object_action.class.php 460 2004-02-17 15:34:52Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/util/complex_array.class.php');
require_once(LIMB_DIR . 'core/actions/form_action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/required_rule.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/tree_identifier_rule.class.php');
require_once(LIMB_DIR . 'core/model/search/full_text_indexer.class.php');

class form_create_site_object_action extends form_action
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
		$parent_object_data =& fetch_mapped_by_url();
		
		$this->validator->add_rule(new required_rule('identifier'));
		$this->validator->add_rule(new tree_identifier_rule('identifier', $parent_object_data['node_id']));
	}
	
	function _valid_perform()
	{
		$parent_object_data =& fetch_mapped_by_url();
		
		$data['parent_id'] = $parent_object_data['node_id'];
		
		$this->_valid_perform_prepare_data($data);
		
		$object =& site_object_factory :: create($this->definition['site_object']);
		$object->import_attributes($data);

		if(($object_id = $this->_create_object_operation($object)) === false)
			return false;
			
		$this->indexer->add($object);
		
		$this->_write_create_access_policy($object);
		
		if(!isset($_REQUEST['popup']) || !$_REQUEST['popup'])
			return $object_id;
			
		close_popup();
	}
	
	function _create_object_operation(&$object)
	{
		if(!$object_id = $object->create())
			return false;

		return $object_id;
	}
	
	function _write_create_access_policy(& $object)
	{
		$parent_data =& fetch_mapped_by_url();

		$parent_object =& site_object_factory :: instance($parent_data['class_name']);
		
		$parent_object->import_attributes($parent_data);

		$access_policy =& access_policy :: instance();
		
		$access_policy->save_object_access($object, $parent_object);
	}
	
	function _valid_perform_prepare_data(&$data)
	{
		complex_array :: map($this->definition['datamap'], $this->dataspace->export(), $data);
	}
}

?>