<?php 
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: nested_sets_driver.class.php 131 2004-04-09 14:11:45Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . 'core/tree/drivers/tree_driver.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

class materialized_path_driver extends tree_driver
{
	var $_db = null;
	
	/**
	* 
	* @var array The field parameters of the table with the nested set.
	* @access public 
	*/
	var $_params = array(
		'id' => 'id',
		'root_id' => 'root_id',
		'identifier' => 'identifier',
		'object_id' => 'object_id',
		'ordr' => 'ordr',
		'path' => 'path',
		'level' => 'level', 
		'parent_id' => 'parent_id',
	);
	
	var $_expanded_parents = array(); 
		
	/**
	* 
	* @var string The table with the actual tree data
	* @access public 
	*/
	var $_node_table = 'sys_site_object_tree';

	/**
	* 
	* @var array An array of field ids that must exist in the table
	* @access private 
	*/
	var $_required_params = array('id', 'root_id', 'path', 'ordr', 'level');

	/**
	* Used for _internal_ tree conversion
	* 
	* @var bool Turn off user param verification and id generation
	* @access private 
	*/
	var $_dumb_mode = false;

	/**
	* Constructor
	* 
	* @param array $params Database column fields which should be returned
	* @access private 
	* @return void 
	*/
	function materialized_path_driver()
	{		
		$this->_db =& db_factory :: instance();
		
		parent :: tree_driver();
	} 
			    
	/**
	* Fetch the whole nested set
	* 
	* @param array $add_sql (optional) Array of additional params to pass to the sql_exec.
	* @access public 
	* @return mixed False on error, or an array of nodes
	*/
	function & get_all_nodes($add_sql = array())
	{
		$node_set = array();
		$rootnodes = $this->get_root_nodes(true);
		foreach($rootnodes AS $rid => $rootnode)
		{
			$node_set = $node_set + $this->get_branch($rid, true);
		} 
		return $node_set;
	} 
	/**
	* Fetches the first level (the rootnodes) of the NestedSet
	* 
	* @param array $add_sql (optional) Array of additional params to pass to the sql_exec.
	* @see _add_sql
	* @access public 
	* @return mixed False on error, or an array of nodes
	*/
	function & get_root_nodes($add_sql = array())
	{
		$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.id=%s.root_id %s ORDER BY %s.%s ASC',
										$this->_get_select_fields(),
										$this->_add_sql($add_sql, 'columns'),
										$this->_node_table,
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table,
										$this->_node_table,
										$this->_add_sql($add_sql, 'append'),
										$this->_node_table, $this->_secondary_sort);

		$node_set =& $this->_get_result_set($sql);

		return $node_set;
	} 
	/**
	* Fetch the whole branch where a given node id is in
	* 
	* @param int $id The node ID
	* @param array $add_sql (optional) Array of additional params to pass to the sql_exec.
	* @see _add_sql
	* @access public 
	* @return mixed False on error, or an array of nodes
	*/
	function & get_branch($id, $add_sql = array())
	{
		if (!($this_node = $this->get_node($id)))
		{
    	debug :: write_error('TREE_ERROR_NODE_NOT_FOUND',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
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

		$node_set =& $this->_get_result_set($sql);

		return $node_set;
	} 
	/**
	* Fetch the parents of a node given by id
	* 
	* @param int $id The node ID
	* @param array $add_sql (optional) Array of additional params to pass to the sql_exec.
	*/
	function & get_parents($id, $add_sql = array())
	{
		if (!$child = $this->get_node($id))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 
		
		$join_table = $this->_node_table . '2';
		
		$sql = sprintf("SELECT %s %s 
										FROM {$this->_node_table}, {$this->_node_table} AS  {$join_table} %s
                    WHERE 
                    {$join_table}.path LIKE %s AND 
                    {$this->_node_table}.root_id = {$child['root_id']} AND 
                    {$this->_node_table}.level < {$child['level']} AND 
                    {$join_table}.id = {$child['id']}
                    %s
                    ORDER BY {$this->_node_table}.level ASC",
										$this->_get_select_fields(),
										$this->_add_sql($add_sql, 'columns'),
										$this->_add_sql($add_sql, 'join'),
										$this->_db->concat(array($this->_node_table . '.path', '"%"')),
										$this->_add_sql($add_sql, 'append')
									);

		$node_set =& $this->_get_result_set($sql);

		return $node_set;
	} 
	
	/**
	* Fetch the immediate parent of a node given by id
	* 
	* @param int $id The node ID
	* @param array $add_sql (optional) Array of additional params to pass to the sql_exec.
	* @see _add_sql
	* @access public 
	* @return mixed False on error, or the parent node
	*/
	function & get_parent($id, $add_sql = array())
	{
		if (!$child = $this->get_node($id))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
			return false;
		} 

		if ($child['id'] == $child['root_id'])
			return false;
		
		return $this->get_node($child['parent_id'], $add_sql);
	} 
	
	/**
	* Fetch all siblings of the node given by id
	* Important: The node given by ID will also be returned
	* Do a unset($array[$id]) on the result if you don't want that
	* 
	* @param int $id The node ID
	* @param array $add_sql (optional) Array of additional params to pass to the sql_exec.
	* @see _add_sql
	* @access public 
	* @return mixed False on error, or the parent node
	*/
	function & get_siblings($id, $add_sql = array())
	{
		if (!($sibling = $this->get_node($id)))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 

		$parent = $this->get_parent($sibling['id']);
		return $this->get_children($parent['id'], $add_sql);
	} 
	
	/**
	* Fetch the children _one level_ after of a node given by id
	* 
	* @param int $id The node ID
	* @param bool $force_ordr (optional) Force the result to be ordered by the ordr
	*              param (as opposed to the value of secondary sort).  Used by the move and
	*              add methods.
	* @param array $add_sql (optional) Array of additional params to pass to the sql_exec.
	* @see _add_sql
	* @access public 
	* @return mixed False on error, or an array of nodes
	*/
	function & get_children($id, $add_sql = array())
	{		
		if (!$parent = $this->get_node($id))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
			return false;
		} 

		$sql = sprintf('SELECT %s %s FROM %s %s
                    WHERE %s.parent_id=%s %s',
										$this->_get_select_fields(),
										$this->_add_sql($add_sql, 'columns'),
										$this->_node_table, 
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table, $parent['id'],
										$this->_add_sql($add_sql, 'append'));

		$node_set =& $this->_get_result_set($sql);

		return $node_set;
	} 
	
	function count_children($id, $add_sql=array())
	{
		if (!$parent = $this->get_node($id))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
			return false;
		} 
		
		$sql = sprintf('SELECT count(id) as counter FROM %s %s
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
	* 
	* @param string $id The node ID
	* @param array $add_sql (optional) Array of additional params to pass to the sql_exec.
	* @see _add_sql
	* @access public 
	* @return mixed False on error, or an array of nodes
	*/
	function & get_sub_branch($id, $add_sql = array(), $include_parent = false)
	{
		if (!($parent = $this->get_node($id)))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
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

	function get_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $only_parents = false, $sql_add = array())
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
		
		if($only_parents)
		{
			$sql_add['join'][] = ', sys_class as sc';
			$sql_add['append'][] = ' AND sc.id = sso.class_id AND sc.can_be_parent = 1';
		}
		
 		$nodes =& $this->get_sub_branch($parent_node['id'], $sql_add, $include_parent);
  		
		return $nodes;
	}	
	
	function & get_accessible_sub_branch_by_path($path, $depth = -1, $include_parent = false, $check_expanded_parents = false, $class_id = null, $only_parents = false)
	{
		$sql_add['columns'][] = ', soa.object_id';
		$sql_add['join'][] = ', sys_site_object as sso, sys_object_access as soa';
		$sql_add['append'][] = ' AND sso.id = ' . $this->_node_table . '.object_id AND sso.id = soa.object_id AND soa.r = 1';
	
		$access_policy =& access_policy :: instance();
    $accessor_ids = implode(',', $access_policy->get_accessor_ids());
			
		if ($class_id)
			$sql_add['append'][] = " AND sso.class_id = {$class_id}";
			
		$sql_add['append'][] = " AND soa.accessor_id IN ({$accessor_ids})";

		$result =& $this->get_sub_branch_by_path($path, $depth, $include_parent, $check_expanded_parents, $only_parents, $sql_add);
		
		return $result;
	}
	
	function count_accessible_children($id)
	{
		if (!($parent = $this->get_node($id)))
		{
    	debug :: write_error('node not found',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
			return false;
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
		
		$this->_db->sql_exec($sql);
		
		return count($this->_db->get_array());		
	}
	
	/**
	* Fetch the data of a node with the given id
	* 
	* @param int $id The node id of the node to fetch
	* @param array $add_sql (optional) Array of additional params to pass to the sql_exec.
	* @see _add_sql
	* @access public 
	* @return mixed False on error, or an array of nodes
	*/
	function & get_node($id, $add_sql = array())
	{
		$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.id=%s %s',
										$this->_get_select_fields(), 
										$this->_add_sql($add_sql, 'columns'),
										$this->_node_table, 
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table, $id,
										$this->_add_sql($add_sql, 'append')
									);

		$node_set =& $this->_get_result_set($sql);

		return current($node_set);
	}
	
	function & get_node_by_path($path, $delimiter='/', $recursive = false)
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
	
	function & get_nodes_by_ids($ids)
	{
		$nodes =& $this->get_all_nodes(
			array(
				'append' => array('WHERE ' . sql_in('id', $ids))
			)
		);
		
		return $nodes;
	}

	function get_max_child_identifier($id)
	{
		if (!($parent = $this->get_node($id)))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
			return false;
		} 
		
		$sql = sprintf('SELECT identifier FROM %s
                    WHERE root_id=%s AND level=%s+1 AND l BETWEEN %s AND %s
                    ORDER BY identifier DESC',
										$this->_node_table, 
										$parent['root_id'],
										$parent['level'],
										$parent['l'], $parent['r']);
										
		$this->_db->sql_exec($sql, 1, 0);
		
		if($row =& $this->_db->fetch_row())
			return $row['identifier'];
		else
			return 0;
	}
	
	function is_node($id)
	{
		return ($this->get_node($id) !== false);
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
  	}
  	elseif($direction == 'down' && $pos < (sizeof($children_keys) - 1))	
  	{
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
	* 
	* @param array $values Hash with param => value pairs of the node (see $this->_params)
	* @param integer $id ID of target node (the rootnode after which the node should be inserted)
	* @access public 
	* @return mixed The node id or false on error
	*/
	function create_root_node($values)
	{
		$this->_verify_user_values($values); 

		if (!$this->_dumb_mode)
			$values['id'] = $node_id = $this->_db->get_max_column_value($this->_node_table, 'id') + 1;
		else
			$node_id = $values['id'];

		$values['root_id'] = $node_id;
		$values['path'] = '/' . $node_id . '/';
		$values['level'] = 1;
		$values['ordr'] = $this->_get_next_item_order(0);
		$values['parent_id'] = 0;
		
		$this->_db->sql_insert($this->_node_table, $values);
				
		return $node_id;
	} 
	
  function _get_next_item_order($parent_id)
  {
		$query = sprintf(	'SELECT MAX(ordr) as max_order FROM %s WHERE parent_id=%s', 
											$this->_node_table,
											$parent_id);
		  			
		$this->_db->sql_exec($query);
		$row = $this->_db->fetch_row(); 
		return isset($row['max_order']) ? ($row['max_order'] + 1) : 1;
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
	* 
	* @param integer $parent_id Parent node ID
	* @param array $values Hash with param => value pairs of the node (see $this->_params)
	* @access public 
	* @return mixed The node id or false on error
	*/
	function create_sub_node($parent_id, $values)
	{
		if (!$parent_node = $this->get_node($parent_id))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('parent_id' => $parent_id)
    	);
    	return false;
		} 
		
		$this->_verify_user_values($values); 
		
		if (!$this->_dumb_mode)
		{
			$node_id = $this->_db->get_max_column_value($this->_node_table, 'id') + 1;			
			$values['id'] = $node_id;
		} 
		else
			$node_id = $values['id'];
		
		$values['root_id'] = $parent_node['root_id'];
		$values['level'] = $parent_node['level'] + 1;
		$values['ordr'] = $this->_get_next_item_order($parent_id);
		$values['parent_id'] = $parent_id;			
		$values['path'] = $parent_node['path'] . $node_id . '/';
		
		$this->_db->sql_insert($this->_node_table, $values);

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
	* @param int $id Target node ID
	* @param array $values Hash with param => value pairs of the node (see $this->_params)
	* @access public 
	* @return mixed The node id or false on error
	*/
	function create_left_node($id, $values)
	{
		$this->_verify_user_values($values); 
		
		if (!$target_node = $this->get_node($id))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 

		if ($target_node['root_id'] == $target_node['id'])
		{
    	debug :: error('cant create node before root node',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);

			return false;
		} 

		if (!$this->_dumb_mode)
		{
			$node_id = $values['id'] = $this->_db->get_max_column_value($this->_node_table, 'id') + 1;
		} 
		else
		{
			$node_id = $values['id'];
		} 

		$values['ordr'] = $target_node['ordr'];
		$values['parent_id'] = $target_node['parent_id'];
		$values['root_id'] = $target_node['root_id'];
		$values['level'] = $target_node['level'];
		$values['path'] = preg_replace('~^(.*)(/[^/]+)/$~', '\\1/' . $node_id . '/', $target_node['path']);

		$this->_db->sql_exec(sprintf('UPDATE %s SET ordr=ordr+1
						                      WHERE ordr>=%s AND parent_id=%s',
																	$this->_node_table,
																	$target_node['ordr'],
																	$target_node['parent_id'])); 

		$this->_db->sql_insert($this->_node_table, $values);
		
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
	* @param int $id Target node ID
	* @param array $values Hash with param => value pairs of the node (see $this->_params)
	* @access public 
	* @return mixed The node id or false on error
	*/
	function create_right_node($id, $values)
	{
		$this->_verify_user_values( $values); 
		
		if (!$target_node = $this->get_node($id))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 

		if ($target_node['root_id'] == $target_node['id'])
		{
    	debug :: error('cant create node after root node',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);

			return false;
		} 

		if (!$this->_dumb_mode)
		{
			$node_id = $values['id'] = $this->_db->get_max_column_value($this->_node_table, 'id') + 1;
		} 
		else
		{
			$node_id = $values['id'];
		} 

		$values['ordr'] = $target_node['ordr'] + 1;
		$values['parent_id'] = $target_node['parent_id'];
		$values['root_id'] = $target_node['root_id'];
		$values['level'] = $target_node['level'];
		$values['path'] = preg_replace('~^(.*)(/[^/]+)/$~', '\\1/' . $node_id . '/', $target_node['path']);

		$this->_db->sql_exec(sprintf('UPDATE %s SET ordr=ordr+1
						                      WHERE ordr>%s AND parent_id=%s',
																	$this->_node_table,
																	$target_node['ordr'],
																	$target_node['parent_id'])); 

		$this->_db->sql_insert($this->_node_table, $values);
		
		return $node_id;
	} 
	
	/**
	* Deletes a node
	* 
	* @param int $id ID of the node to be deleted
	* @access public 
	* @return bool True if the delete succeeds
	*/
	function delete_node($id)
	{
		if (!$node = $this->get_node($id))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 
		
		$this->_db->sql_exec("	DELETE FROM {$this->_node_table}
														WHERE 
														path LIKE '{$node['path']}%' AND 
														root_id={$node['root_id']}");

		$this->_db->sql_exec("	UPDATE {$this->_node_table} SET ordr=ordr-1 
														WHERE 
														parent_id={$node['root_id']} AND 
														ordr > {$node['ordr']}");
		return true;
	} 
	
	/**
	* Changes the payload of a node
	* 
	* @param int $id Node ID
	* @param array $values Hash with param => value pairs of the node (see $this->_params)
	* @param bool $_intermal Internal use only. Used to skip value validation. Leave this as it is.
	* @access public 
	* @return bool True if the update is successful
	*/
	function update_node($id, $values, $internal = false)
	{
		if (!$internal)
		{
			$this->_verify_user_values($values);
		} 

		$insert_data = array();
		if (!$qr = $this->_values2update_query($values, $insert_data))
		{
			return false;
		} 

		$sql = sprintf('UPDATE %s SET %s WHERE id=%s',
										$this->_node_table,
										$qr,
										$id);
										
		$this->_db->sql_exec($sql);
		return true;
	} 
	
	/**
	* Wrapper for node moving and copying
	* 
	* @param int $id Source ID
	* @param int $target Target ID
	* @param constant $pos Position (use one of the NESE_MOVE_* constants)
	* @param bool $copy Shall we create a copy
	* @access public 
	* @return int ID of the moved node or false on error
	*/
	function move_tree($id, $target_id, $pos, $copy = false)
	{
		if ($id == $target_id && !$copy)
		{
    	debug :: write_error(NESE_ERROR_RECURSION,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
    		 array('id' => $id, 'target_id' => $target_id)
    	);
    	return false;
		} 
		// Get information about source and target
		if (!($source = $this->get_node($id)))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 

		if (!($target = $this->get_node($target_id)))
		{
    	debug :: write_error(TREE_ERROR_NODE_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('target_id' => $target_id)
    	);
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
	    	debug :: write_error(NESE_ERROR_RECURSION,
	    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
	    		 array('id' => $id, 'target_id' => $target_id)
	    	);
	    	return false;
			} 
			// Insert/move before or after
			if (($source['root_id'] == $source['id']) &&
					($target['root_id'] == $target['id']) && ($pos != NESE_MOVE_BELOW))
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
    	debug :: write_error(NESE_ERROR_RECURSION,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
    		 array('id' => $id, 'target_id' => $target_id)
    	);
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
	* 
	* @param object $ NodeCT $source   Source node
	* @param object $ NodeCT $target   Target node
	* @param string $pos Position [SUBnode/BEfore]
	* @param bool $copy Shall we create a copy
	* @access private 
	*/
	function _move_across($source, $target, $pos, $first = false)
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
			case NESE_MOVE_BEFORE:
				$clone_id = $this->create_left_node($target['id'], $values);
				break;

			case NESE_MOVE_AFTER:
				$clone_id = $this->create_right_node($target['id'], $values);
				break;

			case NESE_MOVE_BELOW:
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
			$pos = NESE_MOVE_BELOW;
			$sclone_id = $clone_id; 
			// Recurse through the child nodes
			foreach($children AS $cid => $child)
			{
				$sclone = $this->get_node($sclone_id);
				$sclone_id = $this->_move_across($child, $sclone, $pos);

				$pos = NESE_MOVE_AFTER;
			} 
		} 

		$this->_relations[$source['id']]['clone_id'] = $clone_id;
		$this->_relations[$source['id']]['parent_id'] = $t_parent_id;

		return $clone_id;
	} 
	/**
	* Deletes the old subtree (node) and writes the node id's into the cloned tree
	* 
	* @param array $relations Hash in der Form $h[alteid]=neueid
	* @param array $copy Are we in copy mode?
	* @access private 
	*/
	function _move_cleanup($copy = false)
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
	* Adds a specific type of SQL to a sql_exec string
	* 
	* @param array $add_sql The array of SQL strings to add.  Example value:
	*                $add_sql = array(
	*                'columns' => 'tb2.col2, tb2.col3',         // Additional tables/columns
	*                'join' => 'LEFT JOIN tb1 USING(id)', // Join statement
	*                'append' => 'GROUP by tb1.id');      // Group condition
	* @param string $type The type of SQL.  Can be 'columns', 'join', or 'append'.
	* @access private 
	* @return string The SQL, properly formatted
	*/
	function _add_sql($add_sql, $type)
	{
		if (!isset($add_sql[$type]))
			return '';

		return implode(' ', $add_sql[$type]);
	} 
	/**
	* Gets the select fields based on the params
	* 
	* @access private 
	* @return string A string of sql_exec fields to select
	*/
	function _get_select_fields()
	{
		$sql_exec_fields = array();
		foreach ($this->_params as $key => $val)
		{
			$sql_exec_fields[] = $this->_node_table . '.' . $key . ' AS ' . $val;
		} 

		return implode(', ', $sql_exec_fields);
	} 
	
	function & _get_result_set($sql)
	{
		$this->_db->sql_exec($sql);
		$nodes =& $this->_db->get_array('id');

		return $nodes;
	} 
	
	function _assign_result_set(&$nodes, $sql)
	{
		$this->_sql = $sql;
		$this->_db->sql_exec($sql);
		$this->_db->assign_array($nodes, 'id');
	} 
						
	/**
	* Clean values from protected or unknown columns
	* 
	* @var string $caller The calling method
	* @var string $values The values array
	* @access private 
	* @return void 
	*/
	function _verify_user_values(&$values)
	{
		if ($this->_dumb_mode)
			return true;

		foreach($values as $field => $value)
		{
			if (!isset($this->_params[$field]))
			{
	    	debug :: write_error(TREE_ERROR_NODE_WRONG_PARAM,
	    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
	    		 array('param' => $field)
	    	);
				unset($values[$field]);
				continue;
			}
			 
			if (in_array($this->_params[$field], $this->_required_params))
			{
	    	debug :: write_error(TREE_ERROR_NODE_WRONG_PARAM,
	    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		 array('value' => $field)
	    	);

				unset($values[$field]);
			} 
		} 
	} 	
} 

?>