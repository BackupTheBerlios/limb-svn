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
require_once(dirname(__FILE__) . '/../site_structure/tree_toggle_action.class.php');

class group_objects_access_tree_toggle_action extends tree_toggle_action
{
	protected $objects_ids = array();

	protected function _define_dataspace_name()
	{
	  return 'set_group_access';
	}
	
	public function perform($request, $response)
	{				
		if ($filter_groups = session :: get('filter_groups'))
			$this->dataspace->set('filter_groups', $filter_groups);	

		parent :: perform($request, $response);

		$this->_set_template_tree();
		$this->_init_dataspace($request);
	}

	protected function _init_dataspace($request)
	{
		$data['policy'] = access_policy :: instance()->get_group_object_access_by_ids($this->object_ids);

		$this->dataspace->merge($data);
	}

	protected function _set_template_tree()
	{
		$datasource = datasource_factory :: create('group_object_access_datasource');
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
		$dataset = $datasource->get_dataset($count, $params);

		$this->object_ids = array();
		$dataset->reset();
		while($dataset->next())
		{
			$object = $dataset->export();
			$this->object_ids[$object['id']] = $object['id'];
		}

		$dataset->reset();		
		$access_tree = $this->view->find_child('access');
		$access_tree->register_dataset($dataset);
	}	
}

?>
