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
require_once(LIMB_DIR . 'core/fetcher.class.php');

class form_create_site_object_action extends form_site_object_action
{
  function _define_controller_name()
  {
    return get_class($this->object) . '_controller';
  }
  
  function _get_controller_id()
  {			
		$controller_name = $this->_define_controller_name();
		
    return site_object_controller :: get_id($controller_name);
  }
  
  function _init_dataspace(&$request)
  {
    parent :: _init_dataspace($request);
    
    if (($parent_node_id = $this->dataspace->get('parent_node_id')) === null)
    {
      $parent_object_data =& $this->_load_parent_object_data();
      $this->dataspace->set('parent_node_id', $parent_object_data['node_id']);
    }
  }
  
	function _init_validator()
	{		
		if (($parent_node_id = $this->dataspace->get('parent_node_id')) === null)
		{
		  if(!$parent_object_data =& $this->_load_parent_object_data())
		  	return;
		  	
			$parent_node_id = $parent_object_data['parent_node_id'];		
		}	
		
		$this->validator->add_rule($v1 = array(LIMB_DIR . 'core/lib/validators/rules/tree_node_id_rule', 'parent_node_id'));
		
		if($this->object->is_auto_identifier())
			return;

		$this->validator->add_rule($v2 = array(LIMB_DIR . 'core/lib/validators/rules/required_rule', 'identifier'));
		$this->validator->add_rule($v3 = array(LIMB_DIR . 'core/lib/validators/rules/tree_identifier_rule', 'identifier', $parent_node_id));
	}
	
	function _valid_perform(&$request, &$response)
	{
		$parent_object_data =& $this->_load_parent_object_data();
		
		$data['parent_node_id'] = $parent_object_data['node_id'];
		$data['controller_id'] = $this->_get_controller_id();
		
		$this->_valid_perform_prepare_data($data);
		
		$this->object->import_attributes($data);

		if($this->_create_object_operation() === false)
		{
		  $request->set_status(REQUEST_STATUS_FAILURE);
			return;
		}
			
		$this->indexer->add($this->object);
		
		$this->_write_create_access_policy();
		
		$request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
		
		if($request->has_attribute('popup'))
			$response->write(close_popup_response($request));
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

		$parent_object =& site_object_factory :: create($parent_data['class_name']);
		
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
		$result =& fetch_requested_object();
		return $result;
	}
}
?>