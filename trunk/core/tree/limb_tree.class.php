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

require_once(LIMB_DIR . 'core/tree/nested_db_tree.class.php');
require_once(LIMB_DIR . 'core/lib/session/session.class.php');

class limb_tree extends nested_db_tree
{
	var $_params = array(
		'id' => 'id',
		'root_id' => 'root_id',
		'identifier' => 'identifier',
		'object_id' => 'object_id',
		'l' => 'l',
		'r' => 'r',
		'ordr' => 'ordr',
		'level' => 'level', 
		'parent_id' => 'parent_id',
	); 

  var $_expanded_parents = array();
  
	function limb_tree()
	{
		parent :: nested_db_tree();
		
  	$this->_expanded_parents =& session :: get('tree_expanded_parents');
  	
  	$this->check_expanded_parents();
	}
		
	function &instance()
	{
		$obj =&	instantiate_object('limb_tree');
		return $obj;
	}
	
	function & get_nodes_by_ids($ids)
	{
		$nodes =& $this->get_all_nodes(
			array(
				'append' => array('WHERE ' . sql_in('id', $ids))
			)
		);
		
		return $nodes;
	}
		
	function get_path_to_node($node)
	{
		$parents = $this->get_parents($node['id']);
		$path = '';
		foreach($parents as $parent_data)
			$path .= '/' . $parent_data['identifier'];
		
		return $path .= '/' . $node['identifier'];
	}
	
	function get_max_child_identifier($id)
	{
		if (!($parent = $this->get_node($id)))
		{
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
			return false;
		} 
		if (!$parent || $parent['l'] == ($parent['r'] - 1))
		{
			return 0;
		} 

		$sql = sprintf('SELECT identifier FROM %s
                    WHERE root_id=%s AND level=%s+1 AND l BETWEEN %s AND %s
                    ORDER BY identifier DESC',
										$this->_node_table, 
										$parent['root_id'],
										$parent['level'],
										$parent['l'], $parent['r']);
										
		$this->db->sql_exec($sql, 1, 0);
		
		if($row =& $this->db->fetch_row())
			return $row['identifier'];
		else
			return 0;
	}
		
	function get_node_by_path($path, $delimiter='/', $recursive = false)
	{
  	$arr = explode($delimiter, $path);

  	array_shift($arr);
  	
  	if(end($arr) == '')
  		array_pop($arr);
  		
  	if(!count($arr))
  		return false;

  	$nodes = $this->get_all_nodes(
  		array(
  			'append' => array('WHERE identifier IN("' . implode('" , "', $arr) . '") AND level <= ' . sizeof($arr))
  		)
  	);
  	
  	if(!count($nodes))
  		return false;
  	
  	$curr_level = 0;
  	$result_node_id = -1;
  	$parent_id = 0;
  	$path_to_node = '';
  	
  	foreach($nodes as $node)
  	{
  		if ($node['level'] < $curr_level)
  			continue;
  			
  		if($node['identifier'] == $arr[$curr_level] && $node['parent_id'] == $parent_id)
  		{
	  		$parent_id = $node['id'];

  			$curr_level++;
  			$result_node_id = $node['id'];
  			$path_to_node .= $delimiter . $node['identifier'];
  			if ($curr_level == sizeof($arr))
  				break;
  		}
  	}

  	if ($curr_level == sizeof($arr))
  		return isset($nodes[$result_node_id]) ? $nodes[$result_node_id] : false;
  	elseif ($recursive && isset($nodes[$result_node_id]))
  	{
  		$nodes[$result_node_id]['only_parent_found'] = true;
  		$nodes[$result_node_id]['path'] = $path_to_node;
  		return $nodes[$result_node_id];
  	}
  	else
  		return false;	
	}
	
	function get_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $sql_add = array())
	{
		if(!$parent_node = $this->get_node_by_path($path))
			return false;
						
		if ($depth != -1)
			$sql_add['append'][] = ' AND level <=' . ($parent_node['level'] + $depth);
		
		if($check_expanded_parents)
		{
			foreach($this->_expanded_parents as $id => $data)
			{				
				if(	($data['status'] == false) && 
						($data['root_id'] == $parent_node['root_id']) &&
						($data['r'] - $data['l'] > 1) && 
						($parent_node['l'] <= $data['l']) &&
						($parent_node['r'] >= $data['l']))
					$sql_add['append'][] = ' AND (l NOT BETWEEN ' . ($data['l'] + 1). ' AND '  . $data['r'] . ')';
			}
		}
		
		$prev_mode = $this->set_sort_mode(NESE_SORT_PREORDER);	
 		$nodes =& $this->get_sub_branch($parent_node['id'], $sql_add, $include_parent);
 		$this->set_sort_mode($prev_mode);
  		
		return $nodes;
	}
	
	function & get_accessible_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $class_id = null)
	{
		$sql_add['columns'][] = ', soa.object_id';
		$sql_add['join'][] = ', sys_site_object as sso, sys_object_access as soa';
		$sql_add['append'][] = ' AND sso.id = ' . $this->_node_table . '.object_id AND sso.id = soa.object_id AND soa.r = 1';
	
		$access_policy =& access_policy :: instance();
    $accessor_ids = implode(',', $access_policy->get_accessor_ids());
			
		if ($class_id)
			$sql_add['append'][] = " AND sso.class_id = {$class_id}";

		$sql_add['append'][] = " AND soa.accessor_id IN ({$accessor_ids})";

		$result =& $this->get_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $sql_add);
		
		return $result;
	}
	
	function count_accessible_children($id)
	{
		if (!($parent = $this->get_node($id)))
		{
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
			return false;
		} 
		
		if ($parent['l'] == ($parent['r'] - 1))
		{
			return 0;
		} 

		$sql_add['join'][] = ', sys_site_object as sso, sys_object_access as soa';
		$sql_add['append'][] = ' AND sso.id = ' . $this->_node_table . '.object_id AND sso.id = soa.object_id AND soa.r = 1';
	
		$access_policy =& access_policy :: instance();
    $accessor_ids = implode(',', $access_policy->get_accessor_ids());
			
		$sql_add['append'][] = " AND soa.accessor_id IN ({$accessor_ids})";
		$sql_add['group'][] = ' GROUP BY ' . $this->_node_table . '.id';
		
		$sql = sprintf('SELECT count(*) as counter FROM %s %s
                    WHERE %s.root_id=%s AND %s.parent_id=%s %s %s',
										$this->_node_table,
										$this->_add_sql($sql_add, 'join'),
										$this->_node_table, 
										$parent['root_id'],
										$this->_node_table, 
										$id, 
										$this->_add_sql($sql_add, 'append'),
										$this->_add_sql($sql_add, 'group')
									);
		
		$this->db->sql_exec($sql);
		
		return count($this->db->get_array());		
	}
	
  function is_node_expanded($id)
  {
  	if(isset($this->_expanded_parents[$id]))
  		return $this->_expanded_parents[$id]['status'];
  	else
  		return false;
  }
  
  function change_node_order($node_id, $direction)
  {
  	if(!$node = $this->get_node($node_id))
  	{
    	debug :: write_error('node not found',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array(
    			'node_id' => $node_id
    		)
    	);
  		return false;
  	}
  	  	  	
  	if($node['parent_id'] == 0)
  		$children = array($node_id => $node);
  	else
  		$children = $this->get_children($node['parent_id'], true);
  	
  	$children_keys = array_keys($children);
  	$pos = array_search($node_id, $children_keys);
  	
  	$result = false;
  	
  	if($direction == 'up' && $pos > 0)
  	{
  		$target_item = $children[$children_keys[$pos-1]];
  		$result = $this->move_tree($node_id, $target_item['id'], NESE_MOVE_BEFORE);
  	}
  	elseif($direction == 'down' && $pos < (sizeof($children_keys) - 1))	
  	{
  		$target_item = $children[$children_keys[$pos+1]];
  		$result = $this->move_tree($node_id, $target_item['id'], NESE_MOVE_AFTER);
  	}
  	
		if($result)
			$this->check_expanded_parents();
			
		return $result;
  }
  
  function check_expanded_parents()
  {
  	if(!is_array($this->_expanded_parents) || sizeof($this->_expanded_parents) == 0)
  	{
  		$this->reset_expanded_parents();  		
  	}
  	elseif(sizeof($this->_expanded_parents) > 0)
  	{
  		$this->update_expanded_parents();
  	}
  }
  
  function update_expanded_parents()
  {
  	$nodes_ids = array_keys($this->_expanded_parents);
  	
  	$nodes =& $this->get_nodes_by_ids($nodes_ids);
  	
  	foreach($nodes as $id => $node)
  		$this->_set_expanded_parent_status($node, $this->is_node_expanded($id));
  }
	
  function toggle_node($id)
  {
  	if(($node = $this->get_node($id)) === false)  		
  		return false;
		
		$this->_set_expanded_parent_status($node, !$this->is_node_expanded($id));
		  	
  	return true;
  }
  
  function expand_node($id)
  {
  	if(($node = $this->get_node($id)) === false)
  		return false;
  	
  	$this->_set_expanded_parent_status($node, true);
  	  	
  	return true;
  }

  function collapse_node($id)
  {
  	if(($node = $this->get_node($id)) === false)
  		return false;
  		
		$this->_set_expanded_parent_status($node, false);
		    
  	return true;
  }
  
  function reset_expanded_parents()
  {
  	$this->_expanded_parents = array();
  	
  	$parent_nodes = $this->get_all_nodes(array('append' => array(' WHERE r > (l+1) ')));
  	
  	foreach($parent_nodes as $node)
  	{
  		if($node['parent_id'] == 0)
  			$this->_set_expanded_parent_status($node, true);
  		else
  			$this->_set_expanded_parent_status($node, false);
  	}
  }
  
  function _set_expanded_parent_status($node, $status)
  {
  	$id = (int)$node['id'];
		$this->_expanded_parents[$id]['l'] = (int)$node['l'];
		$this->_expanded_parents[$id]['r'] = (int)$node['r'];
		$this->_expanded_parents[$id]['root_id'] = (int)$node['root_id'];
		
		$this->_expanded_parents[$id]['status'] = $status;
  }
  
  function can_add_node($parent_id)
  {
  	if (!$this->is_node($parent_id))
  		return false;
  	else
  		return true;	
  }
  
  function can_delete_node($parent_id)
  {
  	$children = $this->get_children($parent_id);
  	
  	if (($children === false) || !count($children))
  		return true;
  	else
  		return false;
  }
 
} 

?>