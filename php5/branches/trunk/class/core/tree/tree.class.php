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
require_once(LIMB_DIR . 'class/core/session.class.php');
require_once(LIMB_DIR . 'class/core/tree/tree_interface.interface.php');

class tree implements tree_interface
{
  static protected $_instance = null;
  
	protected $_tree_driver = null;

	function __construct()
	{
	  $this->_initialize_tree_driver();
	}
	
	static public function instance()
	{
    if (!self :: $_instance)
      self :: $_instance = new tree();

    return self :: $_instance;	
	}		

	public function set_driver($driver)
	{	
	  $this->_tree_driver = $driver;
	}	
			
	public function get_driver()
	{
		return $this->_tree_driver;
	}

	protected function _initialize_tree_driver()
	{	
		if($this->_tree_driver === null)
		{
		  include_once(LIMB_DIR . 'class/core/tree/drivers/materialized_path_driver.class.php');
			$this->_tree_driver = new materialized_path_driver();
		}
	}
		
	public function initialize_expanded_parents()
	{
		$parents =& session :: get('tree_expanded_parents');
		$this->_tree_driver->set_expanded_parents($parents);
	}
	
	public function is_node($id)
	{
		return $this->_tree_driver->is_node($id);
	}
	
	public function get_node($id)
	{
		return $this->_tree_driver->get_node($id);
	}
	
	public function get_parent($id)
	{
		return $this->_tree_driver->get_parent($id);
	}
	
	public function get_parents($id)
	{
		return $this->_tree_driver->get_parents($id);
	}
	
	public function get_siblings($id)
	{
		return $this->_tree_driver->get_siblings($id);
	}
	
	public function get_children($id)
	{
		return $this->_tree_driver->get_children($id);
	}
	
	public function count_children($id)
	{
		return $this->_tree_driver->count_children($id);
	}
	
	public function create_root_node($values)
	{
		return $this->_tree_driver->create_root_node($values);
	}
	
	public function create_sub_node($id, $values)
	{
		if($node_id = $this->_tree_driver->create_sub_node($id, $values))
			$this->_tree_driver->expand_node($id);
			
		return $node_id;
	}
		
	public function delete_node($id)
	{
		return $this->_tree_driver->delete_node($id);
	}
	
	public function update_node($id, $values, $internal = false)
	{
		return $this->_tree_driver->update_node($id, $values, $internal);
	}
	
	public function move_tree($id, $target_id)
	{
		return $this->_tree_driver->move_tree($id, $target_id);
	}
			
	public function set_dumb_mode($status=true)
	{
		$this->_tree_driver->set_dumb_mode($status);
	}
	
	public function get_all_nodes()
	{
		return $this->_tree_driver->get_all_nodes();
	}
	
	public function get_nodes_by_ids($ids_array)
	{
		return $this->_tree_driver->get_nodes_by_ids($ids_array);
	}
		
	public function get_path_to_node($node)
	{
		if(($parents = $this->_tree_driver->get_parents($node['id'])) === false)
			return false;
		
		$path = '';
		foreach($parents as $parent_data)
			$path .= '/' . $parent_data['identifier'];
		
		return $path .= '/' . $node['identifier'];
	}
	
	public function get_max_child_identifier($id)
	{
		return $this->_tree_driver->get_max_child_identifier($id);
	}
			
	public function get_node_by_path($path, $delimiter='/')
	{
  	return $this->_tree_driver->get_node_by_path($path, $delimiter);	
	}
	
	public function get_sub_branch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false)
	{
		return $this->_tree_driver->get_sub_branch($id, $depth, $include_parent, $check_expanded_parents, $only_parents);
	}
	
	public function get_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false)
	{
		return $this->_tree_driver->get_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $only_parents);
	}

	public function get_accessible_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $class_id = null, $only_parents = false)
	{
		return $this->_tree_driver->get_accessible_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $class_id, $only_parents);
	}
	
	public function get_root_nodes()
	{
		return $this->_tree_driver->get_root_nodes();
	}
	
	public function count_accessible_children($id)
	{
		return $this->_tree_driver->count_accessible_children($id);
	}
	
  public function is_node_expanded($id)
  {
  	return $this->_tree_driver->is_node_expanded($id);
  }
    	
  public function toggle_node($id)
  {		  	
  	return $this->_tree_driver->toggle_node($id);
  }
  
  public function expand_node($id)
  {  	  	
  	return $this->_tree_driver->expand_node($id);
  }

  public function collapse_node($id)
  {		    
  	return $this->_tree_driver->collapse_node($id);
  }
      
  public function can_add_node($id)
  {
  	if (!$this->is_node($id))
  		return false;
  	else
  		return true;
  }
  
  public function can_delete_node($id)
  {
  	$amount = $this->count_children($id);
  	
  	if ($amount === false || $amount == 0)
  		return true;
  	else
  		return false;
  }
 
} 

?>