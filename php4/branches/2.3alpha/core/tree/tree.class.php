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
require_once(LIMB_DIR . '/core/lib/session/session.class.php');

class tree
{
	var $_tree_driver = null;
	
	function tree($driver = null)
	{
		$this->initialize_tree_driver($driver);
	}
	
	function &instance($driver = null)
	{
		$obj =&	instantiate_object('tree', array($driver));
		return $obj;
	}
	
	function & get_driver()
	{
		return $this->_tree_driver;
	}

	function initialize_tree_driver($driver = null)
	{	
		if($driver === null)
		{
		  include_once(LIMB_DIR . '/core/tree/drivers/materialized_path_driver.class.php');
			$this->_tree_driver =& new materialized_path_driver();
		}
		else
		  $this->_tree_driver =& $driver;    		
	}
	
	function initialize_expanded_parents()
	{
		$parents =& session :: get('tree_expanded_parents');
		$this->_tree_driver->set_expanded_parents($parents);	
	}
	
	function is_node($id)
	{
		return $this->_tree_driver->is_node($id);
	}
	
	function & get_node($id)
	{
		return $this->_tree_driver->get_node($id);
	}
	
	function & get_parent($id)
	{
		return $this->_tree_driver->get_parent($id);
	}
	
	function & get_parents($id)
	{
		return $this->_tree_driver->get_parents($id);
	}
	
	function & get_siblings($id)
	{
		return $this->_tree_driver->get_siblings($id);
	}
	
	function & get_children($id)
	{
		return $this->_tree_driver->get_children($id);
	}
	
	function count_children($id)
	{
		return $this->_tree_driver->count_children($id);
	}
	
	function create_root_node($values)
	{
		return $this->_tree_driver->create_root_node($values);
	}
	
	function create_sub_node($id, $values)
	{
		if($node_id = $this->_tree_driver->create_sub_node($id, $values))
			$this->_tree_driver->expand_node($id);
			
		return $node_id;
	}
		
	function delete_node($id)
	{
		return $this->_tree_driver->delete_node($id);
	}
	
	function update_node($id, $values, $internal = false)
	{
		return $this->_tree_driver->update_node($id, $values, $internal);
	}
	
	function move_tree($id, $target_id)
	{
		return $this->_tree_driver->move_tree($id, $target_id);
	}
			
	function set_dumb_mode($status=true)
	{
		$this->_tree_driver->set_dumb_mode($status);
	}
	
	function & get_all_nodes()
	{
		return $this->_tree_driver->get_all_nodes();
	}
	
	function & get_nodes_by_ids($ids_array)
	{
		return $this->_tree_driver->get_nodes_by_ids($ids_array);
	}
		
	function get_path_to_node($node)
	{
		if(($parents = $this->_tree_driver->get_parents($node['id'])) === false)
			return false;
		
		$path = '';
		foreach($parents as $parent_data)
			$path .= '/' . $parent_data['identifier'];
		
		return $path .= '/' . $node['identifier'];
	}
	
	function get_max_child_identifier($id)
	{
		return $this->_tree_driver->get_max_child_identifier($id);
	}
			
	function & get_node_by_path($path, $delimiter='/')
	{
  	return $this->_tree_driver->get_node_by_path($path, $delimiter);	
	}
	
	function & get_sub_branch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false)
	{
		return $this->_tree_driver->get_sub_branch($id, $depth, $include_parent, $check_expanded_parents, $only_parents);
	}
	
	function & get_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false)
	{
		return $this->_tree_driver->get_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $only_parents);
	}

	function & get_accessible_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $class_id = null, $only_parents = false)
	{
		return $this->_tree_driver->get_accessible_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $class_id, $only_parents);
	}
	
	function & get_root_nodes()
	{
		return $this->_tree_driver->get_root_nodes();
	}
	
	function count_accessible_children($id)
	{
		return $this->_tree_driver->count_accessible_children($id);
	}
	
  function is_node_expanded($id)
  {
  	return $this->_tree_driver->is_node_expanded($id);
  }
    	
  function toggle_node($id)
  {		  	
  	return $this->_tree_driver->toggle_node($id);
  }
  
  function expand_node($id)
  {  	  	
  	return $this->_tree_driver->expand_node($id);
  }

  function collapse_node($id)
  {		    
  	return $this->_tree_driver->collapse_node($id);
  }
      
  function can_add_node($id)
  {
  	if (!$this->is_node($id))
  		return false;
  	else
  		return true;
  }
  
  function can_delete_node($id)
  {
  	$amount = $this->count_children($id);
  	
  	if ($amount === false || $amount == 0)
  		return true;
  	else
  		return false;
  }
 
} 

?>