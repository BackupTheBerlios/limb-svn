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
require_once(LIMB_DIR . 'core/fetcher.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');

class form_create_site_object_action extends form_site_object_action
{
	function _init_validator()
	{
		if($this->object->is_auto_identifier())
			return;
		
		$this->validator->add_rule(new required_rule('identifier'));
		
		if($parent_object_data =& $this->_load_parent_object_data())
			$this->validator->add_rule(new tree_identifier_rule('identifier', $parent_object_data['node_id']));
	}
	
	function _valid_perform()
	{
		$parent_object_data =& $this->_load_parent_object_data();
		
		$data['parent_node_id'] = $parent_object_data['node_id'];
		
		$this->_valid_perform_prepare_data($data);
		
		$this->object->import_attributes($data);

		if($this->_create_object_operation() === false)
			return new failed_response();
			
		$this->indexer->add($this->object);
		
		$this->_write_create_access_policy();
		
		if(!isset($_REQUEST['popup']) || !$_REQUEST['popup'])
			return new response(RESPONSE_STATUS_FORM_SUBMITTED);
		else
			return new close_popup_response(RESPONSE_STATUS_FORM_SUBMITTED);
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
		complex_array :: map($this->datamap, $this->dataspace->export(), $data);
	}

	function & _load_parent_object_data()
	{
		$result =& fetch_mapped_by_url();
		return $result;
	}
}
?>