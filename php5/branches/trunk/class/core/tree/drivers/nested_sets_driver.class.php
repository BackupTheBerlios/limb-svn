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
require_once(LIMB_DIR . 'class/core/tree/drivers/tree_db_driver.class.php');

class nested_sets_driver extends tree_db_driver implements tree_interface
{
  const MOVE_BEFORE = 'BE';
  const MOVE_AFTER = 'AF';
  const MOVE_BELOW = 'SUB';
  const SORT_LEVEL = 'SLV';
  const SORT_PREORDER = 'SPO';
  
	protected $_params = array(
		'id' => 'id',
		'root_id' => 'root_id',
		'identifier' => 'identifier',
		'object_id' => 'object_id',
		'l' => 'l',
		'r' => 'r',
		'level' => 'level', 
		'parent_id' => 'parent_id',
	);
	
	protected $_node_table = 'sys_site_object_tree';

	protected $_required_params = array('id', 'root_id', 'l', 'r', 'level');

	protected $_relations = array();
			    
	/**
	* Fetch the whole nested set
	*/
	public function get_all_nodes($add_sql = array())
	{
		$node_set = array();
		$rootnodes = $this->get_root_nodes(true);
		foreach($rootnodes as $rid => $rootnode)
		{
			$node_set = $node_set + $this->get_branch($rid, true);
		} 
		return $node_set;
	} 
	/**
	* Fetches the first level (the rootnodes) of the NestedSet
	*/
	public function get_root_nodes($add_sql = array())
	{
		$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.id=%s.root_id %s',
										$this->_get_select_fields(),
										$this->_add_sql($add_sql, 'columns'),
										$this->_node_table,
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table,
										$this->_node_table,
										$this->_add_sql($add_sql, 'append'));

		return $this->_get_result_set($sql);
	} 
	/**
	* Fetch the whole branch where a given node id is in
	*/
	public function get_branch($id, $add_sql = array())
	{
		if (!($this_node = $this->get_node($id)))
		{
    	return false;
		} 

		$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.root_id=%s %s ORDER BY %s.l ASC',
										$this->_get_select_fields(),
										$this->_add_sql($add_sql, 'columns'),
										$this->_node_table,
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table, $this_node['root_id'],
										$this->_add_sql($add_sql, 'append'),
										$this->_node_table);

		return $this->_get_result_set($sql);
	} 
	/**
	* Fetch the parents of a node given by id
	*/
	public function get_parents($id, $add_sql = array())
	{
		if (!($child = $this->get_node($id)))
		{
    	return false;
		} 

		$sql = sprintf('SELECT %s %s FROM %s %s
                    WHERE %s.root_id=%s AND %s.level < %s AND %s.l < %s AND %s.r > %s %s
                    ORDER BY %s.level ASC',
										$this->_get_select_fields(),
										$this->_add_sql($add_sql, 'columns'),
										$this->_node_table,
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table, $child['root_id'],
										$this->_node_table, $child['level'],
										$this->_node_table, $child['l'],
										$this->_node_table, $child['r'],
										$this->_add_sql($add_sql, 'append'),
										$this->_node_table);

		return $this->_get_result_set($sql);
	} 
	
	/**
	* Fetch the immediate parent of a node given by id
	*/
	public function get_parent($id, $add_sql = array())
	{
		if (!($child = $this->get_node($id)))
		{
			return false;
		} 

		if ($child['id'] == $child['root_id'])
		{
			return false;
		} 
		// If parent node is set inside the db simply return it
		if (isset($child['parent_id']) && !empty($child['parent_id']))
		{
			return $this->get_node($child['parent_id'], $add_sql);
		} 

		$add_sql['append'][] = sprintf('AND %s.level = %s',
			$this->_node_table,
			$child['level']-1);

		$node_set = $this->get_parents($id, $add_sql);

		if (!empty($node_set))
			return current($node_set);
		else
			return false;
	} 
	
	/**
	* Fetch all siblings of the node given by id
	* Important: The node given by ID will also be returned
	* Do aunset($array[$id]) on the result if you don't want that
	*/
	public function get_siblings($id, $add_sql = array())
	{
		if (!($sibling = $this->get_node($id)))
		{
    	return false;
		} 

		$parent = $this->get_parent($sibling['id']);
		return $this->get_children($parent['id'], $add_sql);
	} 
	
	/**
	* Fetch the children _one level_ after of a node given by id
	*/
	public function get_children($id, $add_sql = array())
	{		
		if (!$parent = $this->get_node($id))
		{
			return false;
		} 
		if ($parent['l'] == ($parent['r'] - 1))
		{
			return false;
		} 

		$sql = sprintf('SELECT %s %s FROM %s %s
                    WHERE %s.parent_id=%s %s',
										$this->_get_select_fields(), 
										$this->_add_sql($add_sql, 'columns'),
										$this->_node_table, 
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table, $id,
										$this->_add_sql($add_sql, 'append'));

		return $this->_get_result_set($sql);
	} 
	
	public function count_children($id, $add_sql=array())
	{
		if (!$parent = $this->get_node($id))
		{
			return false;
		} 
		
		if ($parent['l'] == ($parent['r'] - 1))
		{
			return 0;
		} 

		$sql = sprintf('SELECT count(*) as counter FROM %s %s
                    WHERE %s.parent_id=%s %s',
										$this->_node_table,
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table, $id,
										$this->_add_sql($add_sql, 'append'));
		
		$this->_db->sql_exec($sql);
		$dataset = $this->_db->fetch_row();
		
		return (int)$dataset['counter'];
	}
	
	/**
	* Fetch all the children of a node given by id
	* 
	* get_children only queries the immediate children
	* get_sub_branch returns all nodes below the given node
	*/
	public function get_sub_branch($id, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false, $add_sql = array())
	{
		if (!($parent = $this->get_node($id)))
		{
    	return false;
		} 
		
		if ($depth != -1)
			$add_sql['append'][] = " AND {$this->_node_table}.level <=" . ($parent['level'] + $depth);
			
		if($only_parents)
		{
			if(!$this->_is_table_joined('sys_class', $add_sql))
				$add_sql['join'][] = ', sys_class as sc';
				
			if(!$this->_is_table_joined('sys_site_object', $add_sql))
				$add_sql['join'][] = ', sys_site_object as sso';
			
			$add_sql['append'][] = " AND {$this->_node_table}.object_id = sso.id AND sc.id = sso.class_id AND sc.can_be_parent = 1";
		}
		
		if($check_expanded_parents)
		{
			foreach($this->_expanded_parents as $id => $data)
			{				
				if(	($data['status'] == false) && 
						($data['root_id'] == $parent['root_id']) &&
						($data['r'] - $data['l'] > 1) && 
						($parent['l'] <= $data['l']) &&
						($parent['r'] >= $data['l']))
					$sql_add['append'][] = " AND ({$this->_node_table}.l NOT BETWEEN " . ($data['l'] + 1). ' AND '  . $data['r'] . ')';
			}
		}
		
		$sql = sprintf('SELECT %s %s FROM %s %s
                    WHERE %s.l BETWEEN %s AND %s AND %s.root_id=%s AND %s.id!=%s %s
                    ORDER BY %s.l ASC',
										$this->_get_select_fields(), 
										$this->_add_sql($add_sql, 'columns'),
										$this->_node_table, 
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table, $parent['l'], $parent['r'],
										$this->_node_table, $parent['root_id'],
										$this->_node_table, $id,
										$this->_add_sql($add_sql, 'append'), 
										$this->_node_table);
		
		$node_set = array();
		
		if($include_parent)
		{
			$node_set[$id] = $parent;
		}
		
		$this->_assign_result_set($node_set, $sql);
	
		return $node_set;
	} 

	public function get_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false, $add_sql = array())
	{
		if(!$parent_node = $this->get_node_by_path($path))
			return false;
								
 		return $this->get_sub_branch($parent_node['id'], $depth, $include_parent, $check_expanded_parents, $only_parents, $add_sql);
	}	
	
	public function get_accessible_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $class_id = null, $only_parents = false)
	{
		$sql_add['columns'][] = ', soa.object_id';
		$sql_add['join'][] = ', sys_site_object as sso, sys_object_access as soa';
		$sql_add['append'][] = ' AND sso.id = ' . $this->_node_table . '.object_id AND sso.id = soa.object_id AND soa.r = 1';
	
		$access_policy = access_policy :: instance();
    $accessor_ids = implode(',', $access_policy->get_accessor_ids());
			
		if ($class_id)
			$sql_add['append'][] = " AND sso.class_id = {$class_id}";
			
		$sql_add['append'][] = " AND soa.accessor_id IN ({$accessor_ids})";

		return $this->get_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $only_parents, $sql_add);
	}
	
	public function count_accessible_children($id)
	{
		if (!($parent = $this->get_node($id)))
		{
			return false;
		} 
		
		if ($parent['l'] == ($parent['r'] - 1))
		{
			return 0;
		} 

		$sql_add['join'][] = ', sys_site_object as sso, sys_object_access as soa';
		$sql_add['append'][] = ' AND sso.id = ' . $this->_node_table . '.object_id AND sso.id = soa.object_id AND soa.r = 1';
	
		$access_policy = access_policy :: instance();
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
		
		$this->_db->sql_exec($sql);
		
		return count($this->_db->get_array());		
	}
	
	/**
	* Fetch the data of a node with the given id
	*/
	public function get_node($id, $add_sql = array())
	{
		$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.id=%s %s',
										$this->_get_select_fields(), 
										$this->_add_sql($add_sql, 'columns'),
										$this->_node_table, 
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table, $id,
										$this->_add_sql($add_sql, 'append')
									);

		return current($this->_get_result_set($sql));
	}
	
	public function get_node_by_path($path, $delimiter='/', $recursive = false)
	{
  	$arr = explode($delimiter, $path);

  	array_shift($arr);
  	
  	if(end($arr) == '')
  		array_pop($arr);
  		
  	if(!count($arr))
  		return false;

  	$nodes = $this->get_all_nodes(
  		array(
  			'append' => 
  				array('WHERE identifier IN("' . implode('" , "', $arr) . '") AND level <= ' . sizeof($arr))
  		)
  	);
  	
  	if(!$nodes)
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
	
	public function get_nodes_by_ids($ids)
	{
		$nodes = $this->get_all_nodes(
			array(
				'append' => array('WHERE ' . sql_in('id', $ids))
			)
		);
		
		return $nodes;
	}

	public function get_max_child_identifier($id)
	{
		if (!$parent = $this->get_node($id))
		{
			return false;
		} 
		if ($parent['l'] == ($parent['r'] - 1))
		{
			return 0;
		} 

		$sql = sprintf('SELECT identifier FROM %s
                    WHERE parent_id=%s',
										$this->_node_table,
										$id);
										
		$this->_db->sql_exec($sql);		
		if($arr = array_keys($this->_db->get_array('identifier')))
		{
		  uasort($arr, 'strnatcmp');
		  return end($arr);
		}
		else
		  return 0;
	}
	
	public function is_node($id)
	{
		return ($this->get_node($id) !== false);
	}
	
  public function is_node_expanded($id)
  {
  	if(isset($this->_expanded_parents[$id]))
  		return $this->_expanded_parents[$id]['status'];
  	else
  		return false;
  }
  
  public function change_node_order($node_id, $direction)
  {
  	if(!$node = $this->get_node($node_id))
  	{
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
  		$result = $this->move_tree($node_id, $target_item['id'], self :: MOVE_BEFORE);
  	}
  	elseif($direction == 'down' && $pos < (sizeof($children_keys) - 1))	
  	{
  		$target_item = $children[$children_keys[$pos+1]];
  		$result = $this->move_tree($node_id, $target_item['id'], self :: MOVE_AFTER);
  	}
  	
		if($result)
			$this->check_expanded_parents();
			
		return $result;
  }
      
  public function reset_expanded_parents()
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
  
  protected function _set_expanded_parent_status($node, $status)
  {
  	$id = (int)$node['id'];
		$this->_expanded_parents[$id]['l'] = (int)$node['l'];
		$this->_expanded_parents[$id]['r'] = (int)$node['r'];
		$this->_expanded_parents[$id]['root_id'] = (int)$node['root_id'];
		
		$this->_expanded_parents[$id]['status'] = $status;
  }
		
	/**
	* Creates a new root node.  If no id is specified then it is either
	* added to the beginning/end of the tree based on the $pos.
	* Optionally it deletes the whole tree and creates one initial rootnode
	* 
	* <pre>
	* +-- root1 [target]
	* |
	* +-- root2 [new]
	* |
	* +-- root3
	* </pre>
	*/
	public function create_root_node($values)
	{
		$this->_verify_user_values($values); 
	
		if (!$this->_dumb_mode)
			$values['id'] = $node_id = $this->_db->get_max_column_value($this->_node_table, 'id') + 1;
		else
			$node_id = $values['id'];
	
		$values['l'] = 1;
		$values['r'] = 2; 
		$values['root_id'] = $node_id;
		$values['level'] = 1;
		$values['parent_id'] = 0;
		
		$this->_db->sql_insert($this->_node_table, $values);
				
		return $node_id;
	} 
	
	/**
	* Creates a subnode
	* 
	* <pre>
	* +-- root1
	* |
	* +-\ root2 [target]
	* | |
	* | |-- subnode1 [new]
	* |
	* +-- root3
	* </pre>
	*/
	public function create_sub_node($id, $values)
	{
		if (!$parent = $this->get_node($id))
		{
    	return false;
		} 

		$this->_verify_user_values($values); 
		
		// We have children here
		if ($parent['r']-1 != $parent['l'])
		{ 
			$children = $this->get_children($id);
			$last = array_pop($children); 
			// What we have to do is virtually an insert of a node after the last child
			// So we don't have to proceed creating a subnode
			$node_id = $this->create_right_node($last['id'], $values);
			
			return $node_id;
		} 

		$sql = array();
		$sql[] = sprintf('UPDATE %s SET
			                l=CASE WHEN l>%s THEN l+2 ELSE l END,
			                r=CASE WHEN (l>%s OR r>=%s) THEN r+2 ELSE r END
			                WHERE root_id=%s',
											$this->_node_table,
											$parent['l'],
											$parent['l'],
											$parent['r'],
											$parent['root_id']);

		$values['parent_id'] = $parent['id'];
		$values['l'] = $parent['r'];
		$values['r'] = $parent['r'] + 1;
		$values['root_id'] = $parent['root_id'];
		$values['level'] = $parent['level'] + 1;

		if (!$this->_dumb_mode)
		{
			$node_id = $values['id'] = $this->_db->get_max_column_value($this->_node_table, 'id') + 1;
		} 
		else
		{
			$node_id = $values['id'];
		} 
		
		$sql[] = $this->_db->make_insert_string($this->_node_table, $values);
		
		foreach ($sql as $query)
			$this->_db->sql_exec($query);

		return $node_id;
	} 
	
	/**
	* Creates a node before a given node
	* <pre>
	* +-- root1
	* |
	* +-\ root2
	* | |
	* | |-- subnode2 [new]
	* | |-- subnode1 [target]
	* | |-- subnode3
	* |
	* +-- root3
	* </pre>
	* 
	*/
	public function create_left_node($id, $values)
	{
		$this->_verify_user_values($values); 
		// invalid target node, bail out
		if (!($this_node = $this->get_node($id)))
		{
    	return false;
		} 

		// If the target node is a rootnode we virtually want to create a new root node
		if ($this_node['root_id'] == $this_node['id'])
		{
			return $this->create_root_node($values, $id, false, self :: MOVE_BEFORE);
		} 

		$insert_data = array();
		$parent = $this->get_parent($id);
		$insert_data['parent_id'] = $parent['id'];

		$sql = array();
		$sql[] = sprintf('UPDATE %s SET ordr=ordr+1
                      WHERE root_id=%s AND ordr>=%s AND level=%s AND l BETWEEN %s AND %s',
											$this->_node_table,
											$this_node['root_id'],
											$this_node['ordr'],
											$this_node['level'],
											$parent['l'], $parent['r']); 
			
		// Update all nodes which have dependent left and right values
		$sql[] = sprintf('UPDATE %s SET
			                l=CASE WHEN l >= %s THEN l+2 ELSE l END,
			                r=CASE WHEN (r >= %s OR l >= %s) THEN r+2 ELSE r END
			                WHERE root_id=%s',
											$this->_node_table,
											$this_node['l'],
											$this_node['r'],
											$this_node['l'],
											$this_node['root_id']);

		$insert_data['ordr'] = $this_node['ordr'];
		$insert_data['l'] = $this_node['l'];
		$insert_data['r'] = $this_node['l'] + 1;
		$insert_data['root_id'] = $this_node['root_id'];
		$insert_data['level'] = $this_node['level'];

		if (!$this->_dumb_mode || !$node_id = isset($values['id']))
		{
			$node_id = $insert_data['id'] = $this->_db->get_max_column_value($this->_node_table, 'id') + 1;
		} 
		else
		{
			$node_id = $values['id'];
		} 

		if (!$qr = $this->_values2insert_query($values, $insert_data))
		{
			return false;
		} 
		// Insert the new node
		$sql[] = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->_node_table, implode(', ', array_keys($qr)), implode(', ', $qr));
		foreach ($sql as $qry)
		{
			$this->_db->sql_exec($qry);
		} 

		return $node_id;
	} 
	
	/**
	* Creates a node after a given node
	* <pre>
	* +-- root1
	* |
	* +-\ root2
	* | |
	* | |-- subnode1 [target]
	* | |-- subnode2 [new]
	* | |-- subnode3
	* |
	* +-- root3
	* </pre>
	* 
	*/
	public function create_right_node($id, $values)
	{
		if (!$node = $this->get_node($id))
		{
    	return false;
		} 

		if ($node['parent_id'] == 0)
		{
			return false;
		} 

		$this->_verify_user_values($values);
		
		$sql = array();
			
		// Update all nodes which have dependent left and right values
		$sql[] = sprintf('UPDATE %s SET
			                l=CASE WHEN (l > %s AND r > %s) THEN l+2 ELSE l END,
			                r=CASE WHEN r > %s THEN r+2 ELSE r END
			                WHERE root_id=%s',
											$this->_node_table,
											$node['l'],
											$node['r'],
											$node['r'],
											$node['root_id']);

		$values['parent_id'] = $node['parent_id'];
		$values['l'] = $node['r'] + 1;
		$values['r'] = $node['r'] + 2;
		$values['root_id'] = $node['root_id'];
		$values['level'] = $node['level'];
				
		if (!$this->_dumb_mode)
		{
			$node_id = $values['id'] = $this->_db->get_max_column_value($this->_node_table, 'id') + 1;
		} 
		else
		{
			$node_id = $values['id'];
		} 

		$sql[] = $this->_db->make_insert_string($this->_node_table, $values);
													
		foreach ($sql as $query)
			$this->_db->sql_exec($query);
 		
		return $node_id;
	} 
	
	/**
	* Deletes a node
	*/
	public function delete_node($id)
	{
		if (!$node = $this->get_node($id))
		{
    	return false;
		} 

		$len = $node['r'] - $node['l'] + 1;

		$sql = array(); 
		// Delete the node
		$sql[] = sprintf('DELETE FROM %s WHERE l BETWEEN %s AND %s AND root_id=%s',
											$this->_node_table,
											$node['l'], $node['r'],
											$node['root_id']);

		if ($node['parent_id'] != 0)
		{ 
			// The node isn't a rootnode so close the gap
			$sql[] = sprintf('UPDATE %s SET
                        l=CASE WHEN l > %s THEN l - %s ELSE l END,
                        r=CASE WHEN r > %s THEN r - %s ELSE r END
                        WHERE root_id=%s AND (l > %s OR r > %s)',
												$this->_node_table,
												$node['l'],
												$len,
												$node['l'],
												$len,
												$node['root_id'],
												$node['l'],
												$node['r']); 
		} 
		
		foreach ($sql as $qry)
			$this->_db->sql_exec($qry);
			
		return true;
	} 
	
	public function move_tree($id, $target_id)
	{
	  return $this->ns_move_tree($id, $target_id, self :: MOVE_AFTER);
	}
		
	/**
	* Wrapper for node moving and copying
	*/
	public function ns_move_tree($id, $target_id, $pos, $copy = false)
	{
		if ($id == $target_id && !$copy)
		{
    	return false;
		} 
		// Get information about source and target
		if (!($source = $this->get_node($id)))
		{
    	return false;
		} 

		if (!($target = $this->get_node($target_id)))
		{
    	return false;
		} 

		$this->_relations = array(); 

		if (!$copy)
		{ 
			// We have a recursion - let's stop
			if (($target['root_id'] == $source['root_id']) &&
					(($source['l'] <= $target['l']) &&
						($source['r'] >= $target['r'])))
			{
				
	    	return false;
			} 
			// Insert/move before or after
			if (($source['root_id'] == $source['id']) &&
					($target['root_id'] == $target['id']) && ($pos != self :: MOVE_BELOW))
			{ 
				// We have to move a rootnode which is different from moving inside a tree
				$nid = $this->_move_root2root($source, $target, $pos);
				return $nid;
			} 
		} 
		elseif (($target['root_id'] == $source['root_id']) &&
						(	($source['l'] < $target['l']) &&
							($source['r'] > $target['r'])))
		{
    	return false;
		} 
		// We have to move between different levels and maybe subtrees - let's rock ;)
		$move_id = $this->_move_across($source, $target, $pos, true);
		$this->_move_cleanup($copy);

		if (!$copy)
		{
			return $id;
		} 
		else
		{
			return $move_id;
		} 
	} 
	
	/**
	* Moves nodes and trees to other subtrees or levels
	* 
	* <pre>
	* [+]  <--------------------------------+
	* +-[\] root1 [target]                  |
	*      <-------------------------+      |p
	* +-\ root2                      |      |
	* | |                            |      |
	* | |-- subnode1 [target]        |      |B
	* | |-- subnode2 [new]           |S     |E
	* | |-- subnode3                 |U     |F
	* |                              |B     |O
	* +-\ root3                      |      |R
	*    |-- subnode 3.1             |      |E
	*    |-\ subnode 3.2 [source] >--+------+
	*      |-- subnode 3.2.1
	* </pre>
	*/
	protected function _move_across($source, $target, $pos, $first = false)
	{
		// Get the current data from a node and exclude the id params which will be changed
		// because of the node move
		$values = array();
		foreach($this->_params as $key => $val)
		{
			if ($source[$val] && $val != 'parent_id' && !in_array($val, $this->_required_params))
			{
				$values[$key] = trim($source[$val]);
			} 
		} 
		switch ($pos)
		{
			case self :: MOVE_BEFORE:
				$clone_id = $this->create_left_node($target['id'], $values);
				break;

			case self :: MOVE_AFTER:
				$clone_id = $this->create_right_node($target['id'], $values);
				break;

			case self :: MOVE_BELOW:
				$clone_id = $this->create_sub_node($target['id'], $values);
				break;
		} 
		
		$t_parent_id = false;
		if ($first)
		{
			if($t_parent = $this->get_parent($clone_id))
				$t_parent_id = $t_parent['id'];
		} 
		else
			$t_parent_id = $source['parent_id'];

		$children = $this->get_children($source['id']);

		if ($children)
		{
			$pos = self :: MOVE_BELOW;
			$sclone_id = $clone_id; 
			// Recurse through the child nodes
			foreach($children AS $cid => $child)
			{
				$sclone = $this->get_node($sclone_id);
				$sclone_id = $this->_move_across($child, $sclone, $pos);

				$pos = self :: MOVE_AFTER;
			} 
		} 

		$this->_relations[$source['id']]['clone_id'] = $clone_id;
		$this->_relations[$source['id']]['parent_id'] = $t_parent_id;

		return $clone_id;
	} 
	/**
	* Deletes the old subtree (node) and writes the node id's into the cloned tree
	*/
	protected function _move_cleanup($copy = false)
	{
		$relations = $this->_relations;

		$deletes = array();
		$updates = array();
		$parent_updates = array();
		foreach($relations AS $key => $val)
		{
			$clone_id = $val['clone_id'];
			$parent_id = $val['parent_id'];
			$clone = $this->get_node($clone_id);
			if ($copy)
			{ 
				continue;
			} 
			
			if($parent_id !== false)
			{
				$sql = sprintf('UPDATE %s SET parent_id=%s WHERE id=%s',
												$this->_node_table,
												$parent_id,
												$key);
				$parent_updates[] = $sql;
			}

			$deletes[] = $key; 
			// It's isn't a rootnode
			if ($clone['id'] != $clone['root_id'])
			{
				$sql = sprintf('UPDATE %s SET id=%s WHERE id=%s',
												$this->_node_table,
												$key,
												$clone_id);
				$updates[] = $sql;
			} 
			else
			{
				$sql = sprintf('UPDATE %s SET id=%s, root_id=%s WHERE id=%s',
												$this->_node_table,
												$key,
												$clone_id,
												$clone_id);
				$updates[] = $sql;
				$oroot_id = $clone['root_id'];

				$sql = sprintf('UPDATE %s SET root_id=%s WHERE root_id=%s',
												$this->_node_table,
												$key,
												$oroot_id
				);
				$updates[] = $sql;
			} 
		} 

		foreach ($deletes as $delete)
		{
			$this->delete_node($delete);
		} 

		for($i = 0;$i < count($updates);$i++)
		{
			$this->_db->sql_exec($updates[$i]);
		} 

		for($i = 0;$i < count($parent_updates);$i++)
		{
			$this->_db->sql_exec($parent_updates[$i]);
		} 
		
		$this->_relations = array();

		return true;
	} 
	/**
	* Moves rootnodes
	* 
	* <pre>
	* +-- root1
	* |
	* +-\ root2
	* | |
	* | |-- subnode1 [target]
	* | |-- subnode2 [new]
	* | |-- subnode3
	* |
	* +-\ root3
	*   [|]  <-----------------------+
	*    |-- subnode 3.1 [target]    |
	*    |-\ subnode 3.2 [source] >--+
	*      |-- subnode 3.2.1
	* </pre>
	*/
	protected function _move_root2root($source, $target, $pos)
	{
		$tb = $this->_node_table;
		$s_order = $source['ordr'];
		$t_order = $target['ordr'];
		$s_id = $source['id'];
		$t_id = $target['id'];

		if ($s_order < $t_order)
		{
			if ($pos == self :: MOVE_BEFORE)
			{
				$sql = "UPDATE {$tb} SET ordr=ordr-1
                WHERE ordr BETWEEN {$s_order} AND {$t_order} AND
                id!={$t_id} AND
                id!={$s_id} AND
                root_id=id";
                
				$this->_db->sql_exec($sql);
				$sql = "UPDATE {$tb} SET ordr={$t_order}-1 WHERE id={$s_id}";
				$this->_db->sql_exec($sql);
			} 
			elseif ($pos == self :: MOVE_AFTER)
			{
				$sql = "UPDATE {$tb} SET ordr=ordr-1
                WHERE ordr BETWEEN {$s_order} AND {$t_order} AND
                id!={$s_id} AND
                root_id=id";
                
				$this->_db->sql_exec($sql);

				$sql = "UPDATE {$tb} SET ordr={$t_order} WHERE id={$s_id}";
				$this->_db->sql_exec($sql);
			} 
		} 

		if ($s_order > $t_order)
		{
			if ($pos == self :: MOVE_BEFORE)
			{
				$sql = "UPDATE {$tb} SET ordr=ordr+1
                WHERE ordr BETWEEN {$t_order} AND {$s_order} AND
                id != {$s_id} AND
                root_id=id";
				$this->_db->sql_exec($sql);

				$sql = "UPDATE {$tb} SET ordr={$t_order} WHERE id={$s_id}";
				$this->_db->sql_exec($sql);
			} 
			elseif ($pos == self :: MOVE_AFTER)
			{
				$sql = "UPDATE $tb SET ordr=ordr+1
                WHERE ordr BETWEEN $t_order AND $s_order AND
                id!=$t_id AND
                id!=$s_id AND
                root_id=id";
				$this->_db->sql_exec($sql);

				$sql = "UPDATE {$tb} SET ordr={$t_order}+1 WHERE id={$s_id}";
				$this->_db->sql_exec($sql);
			} 
		} 
		return $s_id;
	} 
											
	protected function _values2insert_query($values, $insert_data = false)
	{
		if (is_array($insert_data))
		{
			$values = $values + $insert_data;
		} 

		$arq = array();
		foreach($values as $key => $val)
		{
			$k = $key; 
			$iv = $this->_db->escape(trim($val));
			$arq[$k] = "'$iv'";
		} 

		if (!is_array($arq) || count($arq) == 0)
		{
			return false;
		} 

		return $arq;
	} 
} 

?>