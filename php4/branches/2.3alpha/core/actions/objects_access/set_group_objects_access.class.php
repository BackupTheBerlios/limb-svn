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

class set_group_objects_access extends form_action
{
	var $objects_ids = array();
	
	function _define_dataspace_name()
	{
	  return 'set_group_access';
	}
	
	function perform(&$request, &$response)
	{
		$tree =& tree :: instance();		
		$tree->initialize_expanded_parents();				

		if ($filter_groups = session :: get('filter_groups'))
			$this->dataspace->set('filter_groups', $filter_groups);	
		
		parent :: perform($request, $response);

		$this->_fill_policy();
	}

	function _fill_policy()
	{
		$access_policy =& access_policy :: instance();
		$data['policy'] = $access_policy->get_group_object_access_by_ids($this->object_ids);

		$this->dataspace->merge($data);
	}
	
	function _init_dataspace(&$request)
	{
		parent :: _init_dataspace($request);

		$this->_set_template_tree();
		
		$this->_fill_policy();
	}
	
	function _valid_perform(&$request, &$response)
	{
		$data = $this->dataspace->export();

	  if($groups = $this->dataspace->get('filter_groups'))
	  	session :: set('filter_groups', $groups);
		
		if(isset($data['update']) && isset($data['policy']))
		{
			$access_policy =& access_policy :: instance();
			$access_policy->save_group_object_access($data['policy'], $groups);
		}

		$this->_set_template_tree();
 		
		$request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
	}
	
	function _set_template_tree()
	{
		$datasource =& datasource_factory :: create('group_object_access_datasource');
		$params = array(
			'path' => '/root',
			'depth' => -1,
			'loader_class_name' => 'site_object',
			'restrict_by_class' => false,
			'include_parent' => 'true',
			'check_expanded_parents' => 'true',
			'order' => array('class_ordr' => 'ASC', 'identifier' => 'ASC'),
			'fetch_method' => 'fetch_by_ids'
			
		);
		$count = null;
		$dataset =& $datasource->get_dataset($count, $params);

		$this->object_ids = array();
		$dataset->reset();
		while($dataset->next())
		{
			$object = $dataset->export();
			$this->object_ids[$object['id']] = $object['id'];
		}

		$dataset->reset();		
		$access_tree =& $this->view->find_child('access');
		$access_tree->register_dataset($dataset);
	}
}

?>