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

// Error and message codes
define('NESE_ERROR_RECURSION', 'E100');
define('NESE_ERROR_NODRIVER', 'E200');
define('NESE_ERROR_NOHANDLER', 'E300');
define('NESE_ERROR_TBLOCKED', 'E010');
define('NESE_MESSAGE_UNKNOWN', 'E0');
define('NESE_ERROR_NOTSUPPORTED', 'E1');
define('NESE_ERROR_PARAM_MISSING', 'E400');
define('NESE_ERROR_NOT_FOUND', 'E500');
define('NESE_ERROR_WRONG_MPARAM', 'E2');

// for moving a node before another
define('NESE_MOVE_BEFORE', 'BE');

// for moving a node after another
define('NESE_MOVE_AFTER', 'AF');

// for moving a node below another
define('NESE_MOVE_BELOW', 'SUB');

// Sortorders
define('NESE_SORT_LEVEL', 'SLV');
define('NESE_SORT_PREORDER', 'SPO');

require_once(LIMB_DIR . 'core/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

class nested_db_tree
{
	var $db = null;
	
	/**
	* 
	* @var array The field parameters of the table with the nested set.
	* @access public 
	*/
	var $_params = array(
		'id' => 'id',
		'root_id' => 'root_id',
		'l' => 'l',
		'r' => 'r',
		'ordr' => 'ordr',
		'level' => 'level', 
		'parent_id' => 'parent_id',
	); 
		
	/**
	* 
	* @var string The table with the actual tree data
	* @access public 
	*/
	var $_node_table = 'sys_site_object_tree';

	/**
	* 
	* @var string The table to handle locking
	* @access public 
	*/
	var $_lock_table = 'sys_lock';

	/**
	* Secondary order field.  Normally this is the order field, but can be changed to
	* something else (i.e. the name field so that the tree can be shown alphabetically)
	* 
	* @var string 
	* @access public 
	*/
	var $_secondary_sort = 'ordr';

	/**
	* The default sorting field - will be set to the table column inside the constructor
	* 
	* @var string 
	* @access private 
	*/
	var $_default_secondary_sort = 'ordr';

	/**
	* 
	* @var int The time to live of the lock
	* @access public 
	*/
	var $_lock_ttl = 5;

	/**
	* 
	* @var bool Lock the structure of the table?
	* @access private 
	*/
	var $_structure_table_lock = false;

	/**
	* 
	* @var bool Don't allow unlocking (used inside of moves)
	* @access private 
	*/
	var $_lock_exclusive = false;

	/**
	* Specify the sortMode of the sql_exec methods
	* NESE_SORT_LEVEL is the 'old' sorting method and sorts a tree by level
	* all nodes of level 1, all nodes of level 2,...
	* NESE_SORT_PREORDER will sort doing a preorder walk.
	* So all children of node x will come right after it
	* Note that moving a node within it's siblings will obviously not change the output
	* in this mode
	* 
	* @var constant Order method (NESE_SORT_LEVEL|NESE_SORT_PREORDER)
	* @access private 
	*/
	var $_sort_mode = NESE_SORT_LEVEL;

	/**
	* 
	* @var array Available sortModes
	* @access private 
	*/
	var $_sort_modes = array(NESE_SORT_LEVEL, NESE_SORT_PREORDER);

	/**
	* 
	* @var array An array of field ids that must exist in the table
	* @access private 
	*/
	var $_required_params = array('id', 'root_id', 'l', 'r', 'ordr', 'level');

	/**
	* 
	* @var array Used for mapping a cloned tree to the real tree for move_* operations
	* @access private 
	*/
	var $_relations = array();

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
	function nested_db_tree()
	{		
		$this->db =& db_factory :: instance();
		
		register_shutdown_function(array(&$this, '_nested_db_tree'));
	} 
	/**
	* Destructor
	* Releases all locks
	* Closes open database connections
	* 
	*/
	function _nested_db_tree()
	{
		$this->_release_lock(true);
	} 
	
  function & instance()
  {
		$obj =&	instantiate_object('nested_db_tree');
		return $obj;
  }
  
  function set_dumb_mode($status=true)
  {
  	$this->_dumb_mode = $status;
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
		if ($this->_sort_mode == NESE_SORT_LEVEL)
		{
			$sql = sprintf('SELECT %s %s FROM %s %s %s ORDER BY %s.level, %s.%s ASC',
											$this->_get_select_fields(),
											$this->_add_sql($add_sql, 'columns'),
											$this->_node_table,
											$this->_add_sql($add_sql, 'join'),
											$this->_add_sql($add_sql, 'append'),
											$this->_node_table,
											$this->_node_table, $this->_secondary_sort);
		} 
		elseif ($this->_sort_mode == NESE_SORT_PREORDER)
		{
			$node_set = array();
			$rootnodes = $this->get_root_nodes(true);
			foreach($rootnodes AS $rid => $rootnode)
			{
				$node_set = $node_set + $this->get_branch($rid, true);
			} 
			return $node_set;
		} 

		$node_set =& $this->_get_result_set($sql);

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
    	debug :: write_error('NESE_ERROR_NOT_FOUND',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 
		if ($this->_sort_mode == NESE_SORT_LEVEL)
		{
			$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.root_id=%s %s ORDER BY %s.level, %s.%s ASC',
											$this->_get_select_fields(),
											$this->_add_sql($add_sql, 'columns'),
											$this->_node_table,
											$this->_add_sql($add_sql, 'join'),
											$this->_node_table, $this_node['root_id'],
											$this->_add_sql($add_sql, 'append'),
											$this->_node_table,
											$this->_node_table, $this->_secondary_sort);
		} 
		elseif ($this->_sort_mode == NESE_SORT_PREORDER)
		{
			$sql = sprintf('SELECT %s %s FROM %s %s WHERE %s.root_id=%s %s ORDER BY %s.l ASC',
											$this->_get_select_fields(),
											$this->_add_sql($add_sql, 'columns'),
											$this->_node_table,
											$this->_add_sql($add_sql, 'join'),
											$this->_node_table, $this_node['root_id'],
											$this->_add_sql($add_sql, 'append'),
											$this->_node_table);
		} 

		$node_set =& $this->_get_result_set($sql);

		if ($this->_sort_mode == NESE_SORT_PREORDER && ($this->_secondary_sort != $this->_default_secondary_sort))
		{
			uasort($node_set, array($this, '_sec_sort'));
		} 
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
		if (!($child = $this->get_node($id)))
		{
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
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
		if (!($child = $this->get_node($id)))
		{
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
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

		$node_set =& $this->get_parents($id, $add_sql);

		if (!empty($node_set))
			return current($node_set);
		else
			return false;
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
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
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
			return false;
		} 

		$sql = sprintf('SELECT %s %s FROM %s %s
                    WHERE %s.root_id=%s AND %s.level=%s+1 AND %s.l BETWEEN %s AND %s %s
                    ORDER BY %s.%s ASC',
										$this->_get_select_fields(), 
										$this->_add_sql($add_sql, 'columns'),
										$this->_node_table, 
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table, $parent['root_id'],
										$this->_node_table, $parent['level'],
										$this->_node_table, $parent['l'], $parent['r'],
										$this->_add_sql($add_sql, 'append'),
										$this->_node_table, $this->_secondary_sort);

		$node_set =& $this->_get_result_set($sql);

		return $node_set;
	} 
	
	function count_children($id, $add_sql=array())
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

		$sql = sprintf('SELECT count(*) as counter FROM %s %s
                    WHERE %s.root_id=%s AND %s.parent_id=%s %s',
										$this->_node_table,
										$this->_add_sql($add_sql, 'join'),
										$this->_node_table, $parent['root_id'],
										$this->_node_table, $id, 
										$this->_add_sql($add_sql, 'append'));
		
		$this->db->sql_exec($sql);
		$data_set = $this->db->fetch_row();
		
		return (int)$data_set['counter'];
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
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 
		if ($this->_sort_mode == NESE_SORT_LEVEL)
		{
			$sql = sprintf('SELECT %s %s FROM %s %s
	                    WHERE %s.l BETWEEN %s AND %s AND %s.root_id=%s AND %s.id!=%s %s
	                    ORDER BY %s.level, %s.%s ASC',
											$this->_get_select_fields(), 
											$this->_add_sql($add_sql, 'columns'),
											$this->_node_table, 
											$this->_add_sql($add_sql, 'join'),
											$this->_node_table, $parent['l'], $parent['r'],
											$this->_node_table, $parent['root_id'],
											$this->_node_table, $id,
											$this->_add_sql($add_sql, 'append'),
											$this->_node_table, 
											$this->_node_table, 
											$this->_secondary_sort);
		} 
		elseif ($this->_sort_mode == NESE_SORT_PREORDER)
		{
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
		} 
		
		$node_set = array();
		
		if($include_parent)
		{
			$node_set[$id] = $parent;
		}
		
		$this->_assign_result_set($node_set, $sql);

		if ($this->_secondary_sort != $this->_default_secondary_sort)
		{
			uasort($node_set, array($this, '_sec_sort'));
		} 
		
		return $node_set;
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
	
	function is_node($id)
	{
		return ($this->get_node($id) !== false);
	}
	
	/**
	* See if a given node is a parent of another given node
	* 
	* A node is considered to be a parent if it resides above the child
	* So it doesn't mean that the node has to be an immediate parent.
	* To get this information simply compare the levels of the two nodes
	* after you know that you have a parent relation.
	* 
	* @param mixed $parent The parent node as array or object
	* @param mixed $child The child node as array or object
	* @access public 
	* @return bool True if it's a parent
	*/
	function is_parent($parent_node, $child_node)
	{
		$p_root_id = $parent_node['root_id'];
		$p_l = $parent_node['l'];
		$p_r = $parent_node['r'];

		$c_root_id = $child_node['root_id'];
		$c_l = $child_node['l'];
		$c_r = $child_node['r'];

		if (($p_root_id == $c_root_id) && ($p_l < $c_l && $p_r > $c_r))
			return true;

		return false;
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
	* @param bool $first Danger: Deletes and (re)init's the hole tree - sequences are reset
	* @param string $pos The position in which to insert the new node.
	* @access public 
	* @return mixed The node id or false on error
	*/
	function create_root_node($values, $id = false, $first = false, $pos = NESE_MOVE_AFTER)
	{
		$this->_verify_user_values($values); 
		// If they specified an id, see if the parent is valid
		if (!$first && ($id && !$parent = $this->get_node($id)))
		{
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 
		elseif ($first && $id)
		{ 
    	debug :: write_notice('these 2 params don\'t make sense together',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('first' => $first, 'id' => $id)
    	);
		} 
		elseif (!$first && !$id)
		{ 
			// If no id was specified, then determine order
			$parent = array();
			if ($pos == NESE_MOVE_BEFORE)
			{
				$parent['ordr'] = 1;
			} elseif ($pos == NESE_MOVE_AFTER)
			{ 
				// Put it at the end of the tree
				$qry = sprintf('SELECT MAX(ordr) as m FROM %s WHERE l=1',
					$this->_node_table);
					
				$this->db->sql_exec($qry);
				$tmp_order = $this->db->fetch_row(); 
				// If null, then it's the first one
				$parent['ordr'] = isset($tmp_order['m']) ? $tmp_order['m'] : 0;
			} 
		} 
		// Try to aquire a table lock
		$lock = $this->_set_lock();

		$sql = array();
		$insert_data = array();
		$insert_data['level'] = 1; 
		// Shall we delete the existing tree (reinit)
		if ($first)
		{
			$this->db->sql_delete($this->_node_table);
			$insert_data['ordr'] = 1;
		} 
		else
		{ 
			// Let's open a gap for the new node
			if ($pos == NESE_MOVE_AFTER)
			{
				$insert_data['ordr'] = $parent['ordr'] + 1;
				$sql[] = sprintf('UPDATE %s SET ordr=ordr+1 WHERE l=1 AND ordr > %s',
													$this->_node_table,
													$parent['ordr']);
			} 
			elseif ($pos == NESE_MOVE_BEFORE)
			{
				$insert_data['ordr'] = $parent['ordr'];
				$sql[] = sprintf('UPDATE %s SET ordr=ordr+1 WHERE l=1 AND ordr >= %s',
													$this->_node_table,
													$parent['ordr']);
			} 
		} 

		$insert_data['parent_id'] = 0;
		
		// Sequence of node id (equals to root id in this case
		if (!$this->_dumb_mode 
				|| !isset($values['id'])
				|| !isset($values['root_id']))
		{
			$insert_data['root_id'] = 
			$insert_data['id'] = 
			$node_id =
			$this->db->get_max_column_value($this->_node_table, 'id') + 1;
		} 
		else
		{
			$node_id = $values['id'];
		} 
		// Left/Right values for rootnodes
		$insert_data['l'] = 1;
		$insert_data['r'] = 2; 
		// Transform the node data hash to a sql_exec
		if (!$qr = $this->_values2insert_query($values, $insert_data))
		{
			$this->_release_lock();
			return false;
		} 
		// Insert the new node
		$sql[] = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->_node_table, implode(', ', array_keys($qr)), implode(', ', $qr));

		foreach ($sql as $qry)
		{
			$this->db->sql_exec($qry);
		} 
		
		$this->_release_lock();
		
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
	* 
	* @param integer $id Parent node ID
	* @param array $values Hash with param => value pairs of the node (see $this->_params)
	* @access public 
	* @return mixed The node id or false on error
	*/
	function create_sub_node($id, $values)
	{
		// invalid parent id, bail out
		if (!($this_node = $this->get_node($id)))
		{
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 
		// Try to aquire a table lock
		$lock = $this->_set_lock();

		$this->_verify_user_values($values); 
		// Get the children of the target node
		$children = $this->get_children($id); 
		// We have children here
		if ($this_node['r']-1 != $this_node['l'])
		{ 
			// Get the last child
			$last = array_pop($children); 
			// What we have to do is virtually an insert of a node after the last child
			// So we don't have to proceed creating a subnode
			$new_node = $this->create_right_node($last['id'], $values);
			$this->_release_lock();
			return $new_node;
		} 

		$sql = array();
		$sql[] = sprintf('UPDATE %s SET
			                l=CASE WHEN l>%s THEN l+2 ELSE l END,
			                r=CASE WHEN (l>%s OR r>=%s) THEN r+2 ELSE r END
			                WHERE root_id=%s',
											$this->_node_table,
											$this_node['l'],
											$this_node['l'],
											$this_node['r'],
											$this_node['root_id']);

		$insert_data = array();
		$insert_data['parent_id'] = $this_node['id'];

		$insert_data['l'] = $this_node['r'];
		$insert_data['r'] = $this_node['r'] + 1;
		$insert_data['root_id'] = $this_node['root_id'];
		$insert_data['ordr'] = 1;
		$insert_data['level'] = $this_node['level'] + 1;

		if (!$this->_dumb_mode || !$node_id = isset($values['id']))
		{
			$node_id = $insert_data['id'] = $this->db->get_max_column_value($this->_node_table, 'id') + 1;
		} 
		else
		{
			$node_id = $values['id'];
		} 

		if (!$qr = $this->_values2insert_query($values, $insert_data))
		{
			$this->_release_lock();
			return false;
		} 

		$sql[] = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->_node_table, implode(', ', array_keys($qr)), implode(', ', $qr));
		foreach ($sql as $qry)
		{
			$this->db->sql_exec($qry);
		} 

		$this->_release_lock();
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
	* @param bool $returnID Tell the method to return a node id instead of an object.
	*                                 ATTENTION: That the method defaults to return an object instead of the node id
	*                                 has been overseen and is basically a bug. We have to keep this to maintain BC.
	*                                 You will have to set $returnID to true to make it behave like the other creation methods.
	*                                 This flaw will get fixed with the next major version.
	* @access public 
	* @return mixed The node id or false on error
	*/
	function create_left_node($id, $values)
	{
		$this->_verify_user_values($values); 
		// invalid target node, bail out
		if (!($this_node = $this->get_node($id)))
		{
    	debug :: error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 

		$lock = $this->_set_lock();
		
		// If the target node is a rootnode we virtually want to create a new root node
		if ($this_node['root_id'] == $this_node['id'])
		{
			return $this->create_root_node($values, $id, false, NESE_MOVE_BEFORE);
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
			$node_id = $insert_data['id'] = $this->db->get_max_column_value($this->_node_table, 'id') + 1;
		} 
		else
		{
			$node_id = $values['id'];
		} 

		if (!$qr = $this->_values2insert_query($values, $insert_data))
		{
			$this->_release_lock();
			return false;
		} 
		// Insert the new node
		$sql[] = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->_node_table, implode(', ', array_keys($qr)), implode(', ', $qr));
		foreach ($sql as $qry)
		{
			$this->db->sql_exec($qry);
		} 

		$this->_release_lock();
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
	* @param bool $returnID Tell the method to return a node id instead of an object.
	*                                 ATTENTION: That the method defaults to return an object instead of the node id
	*                                 has been overseen and is basically a bug. We have to keep this to maintain BC.
	*                                 You will have to set $returnID to true to make it behave like the other creation methods.
	*                                 This flaw will get fixed with the next major version.
	* @access public 
	* @return mixed The node id or false on error
	*/
	function create_right_node($id, $values)
	{
		$this->_verify_user_values( $values); 
		// invalid target node, bail out
		if (!($this_node = $this->get_node($id)))
		{
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 

		$lock = $this->_set_lock();
		
		// If the target node is a rootnode we virtually want to create a new root node
		if ($this_node['root_id'] == $this_node['id'])
		{
			$nid = $this->create_root_node($values, $id);
			$this->_release_lock();
			return $nid;
		} 

		$insert_data = array();
		$parent = $this->get_parent($id);
		$insert_data['parent_id'] = $parent['id'];


		$sql = array();
		$sql[] = sprintf('UPDATE %s SET ordr=ordr+1
                      WHERE root_id=%s AND ordr > %s AND level=%s AND l BETWEEN %s AND %s',
											$this->_node_table,
											$this_node['root_id'],
											$this_node['ordr'],
											$this_node['level'],
											$parent['l'], 
											$parent['r']); 
			
		// Update all nodes which have dependent left and right values
		$sql[] = sprintf('UPDATE %s SET
			                l=CASE WHEN (l > %s AND r > %s) THEN l+2 ELSE l END,
			                r=CASE WHEN r > %s THEN r+2 ELSE r END
			                WHERE root_id=%s',
											$this->_node_table,
											$this_node['l'],
											$this_node['r'],
											$this_node['r'],
											$this_node['root_id']);

		$insert_data['ordr'] = $this_node['ordr'] + 1;
		$insert_data['l'] = $this_node['r'] + 1;
		$insert_data['r'] = $this_node['r'] + 2;
		$insert_data['root_id'] = $this_node['root_id'];
		$insert_data['level'] = $this_node['level'];
				
		if (!$this->_dumb_mode || !isset($values['id']))
		{
			$node_id = $insert_data['id'] = $this->db->get_max_column_value($this->_node_table, 'id') + 1;
		} 
		else
		{
			$node_id = $values['id'];
		} 

		if (!$qr = $this->_values2insert_query($values, $insert_data))
		{
			$this->_release_lock();
			return false;
		} 
		// Insert the new node
		$sql[] = sprintf('INSERT INTO %s (%s) VALUES (%s)', 
											$this->_node_table, 
											implode(', ', array_keys($qr)), 
											implode(', ', $qr));
											
		foreach ($sql as $qry)
		{
			$this->db->sql_exec($qry);
		} 
 		
		$this->_release_lock();
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
		// invalid target node, bail out
		if (!($this_node = $this->get_node($id)))
		{
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 

		$lock = $this->_set_lock();

		$parent = $this->get_parent($id);
		$len = $this_node['r'] - $this_node['l'] + 1;

		$sql = array(); 
		// Delete the node
		$sql[] = sprintf('DELETE FROM %s WHERE l BETWEEN %s AND %s AND root_id=%s',
											$this->_node_table,
											$this_node['l'], $this_node['r'],
											$this_node['root_id']);

		if ($this_node['id'] != $this_node['root_id'])
		{ 
			// The node isn't a rootnode so close the gap
			$sql[] = sprintf('UPDATE %s SET
                        l=CASE WHEN l > %s THEN l - %s ELSE l END,
                        r=CASE WHEN r > %s THEN r - %s ELSE r END
                        WHERE root_id=%s AND (l > %s OR r > %s)',
												$this->_node_table,
												$this_node['l'],
												$len,
												$this_node['l'],
												$len,
												$this_node['root_id'],
												$this_node['l'],
												$this_node['r']); 
			// Re-order
			$sql[] = sprintf('UPDATE %s SET ordr=ordr-1 
                    		WHERE root_id=%s AND level=%s AND ordr > %s AND l BETWEEN %s AND %s',
												$this->_node_table,
												$this_node['root_id'],
												$this_node['level'],
												$this_node['ordr'],
												$parent['l'], $parent['r']);
		} 
		else
		{ 
			// A rootnode was deleted and we only have to close the gap inside the order
			$sql[] = sprintf('UPDATE %s SET ordr=ordr-1 WHERE root_id=id AND ordr > %s',
												$this->_node_table,
												$this_node['ordr']);
		} 

		foreach ($sql as $qry)
		{
			$this->db->sql_exec($qry);
		} 
		$this->_release_lock();
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
	function update_node($id, $values, $_internal = false)
	{
		$lock = $this->_set_lock();

		if (!$_internal)
		{
			$this->_verify_user_values($values);
		} 

		$insert_data = array();
		if (!$qr = $this->_values2update_query($values, $insert_data))
		{
			$this->_release_lock();
			return false;
		} 

		$sql = sprintf('UPDATE %s SET %s WHERE id=%s',
										$this->_node_table,
										$qr,
										$id);
										
		$this->db->sql_exec($sql);
		$this->_release_lock();
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
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
    	);
    	return false;
		} 
		// Get information about source and target
		if (!($source = $this->get_node($id)))
		{
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('id' => $id)
    	);
    	return false;
		} 

		if (!($target = $this->get_node($target_id)))
		{
    	debug :: write_error(NESE_ERROR_NOT_FOUND,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array('target_id' => $target_id)
    	);
    	return false;
		} 

		$lock = $this->_set_lock(true);

		$this->_relations = array(); 

		if (!$copy)
		{ 
			// We have a recursion - let's stop
			if (($target['root_id'] == $source['root_id']) &&
					(($source['l'] <= $target['l']) &&
						($source['r'] >= $target['r'])))
			{
				$this->_release_lock(true);
				
	    	debug :: write_error(NESE_ERROR_RECURSION,
	    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
	    	);
	    	return false;
			} 
			// Insert/move before or after
			if (($source['root_id'] == $source['id']) &&
					($target['root_id'] == $target['id']) && ($pos != NESE_MOVE_BELOW))
			{ 
				// We have to move a rootnode which is different from moving inside a tree
				$nid = $this->_move_root2root($source, $target, $pos);
				$this->_release_lock(true);
				return $nid;
			} 
		} 
		elseif (($target['root_id'] == $source['root_id']) &&
						(	($source['l'] < $target['l']) &&
							($source['r'] > $target['r'])))
		{
			$this->_release_lock(true);
			
    	debug :: write_error(NESE_ERROR_RECURSION,
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__
    	);
    	return false;
		} 
		// We have to move between different levels and maybe subtrees - let's rock ;)
		$move_id = $this->_move_across($source, $target, $pos, true);
		$this->_move_cleanup($copy);
		$this->_release_lock(true);

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
			$this->db->sql_exec($updates[$i]);
		} 

		for($i = 0;$i < count($parent_updates);$i++)
		{
			$this->db->sql_exec($parent_updates[$i]);
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
	* 
	* @param object $ NodeCT $source    Source
	* @param object $ NodeCT $target    Target
	* @param string $pos BEfore | AFter
	* @access private 
	* @see moveTree
	*/
	function _move_root2root($source, $target, $pos)
	{
		$lock = $this->_set_lock();

		$tb = $this->_node_table;
		$s_order = $source['ordr'];
		$t_order = $target['ordr'];
		$s_id = $source['id'];
		$t_id = $target['id'];

		if ($s_order < $t_order)
		{
			if ($pos == NESE_MOVE_BEFORE)
			{
				$sql = "UPDATE {$tb} SET ordr=ordr-1
                WHERE ordr BETWEEN {$s_order} AND {$t_order} AND
                id!={$t_id} AND
                id!={$s_id} AND
                root_id=id";
                
				$this->db->sql_exec($sql);
				$sql = "UPDATE {$tb} SET ordr={$t_order}-1 WHERE id={$s_id}";
				$this->db->sql_exec($sql);
			} 
			elseif ($pos == NESE_MOVE_AFTER)
			{
				$sql = "UPDATE {$tb} SET ordr=ordr-1
                WHERE ordr BETWEEN {$s_order} AND {$t_order} AND
                id!={$s_id} AND
                root_id=id";
                
				$this->db->sql_exec($sql);

				$sql = "UPDATE {$tb} SET ordr={$t_order} WHERE id={$s_id}";
				$this->db->sql_exec($sql);
			} 
		} 

		if ($s_order > $t_order)
		{
			if ($pos == NESE_MOVE_BEFORE)
			{
				$sql = "UPDATE {$tb} SET ordr=ordr+1
                WHERE ordr BETWEEN {$t_order} AND {$s_order} AND
                id != {$s_id} AND
                root_id=id";
				$this->db->sql_exec($sql);

				$sql = "UPDATE {$tb} SET ordr={$t_order} WHERE id={$s_id}";
				$this->db->sql_exec($sql);
			} 
			elseif ($pos == NESE_MOVE_AFTER)
			{
				$sql = "UPDATE $tb SET ordr=ordr+1
                WHERE ordr BETWEEN $t_order AND $s_order AND
                id!=$t_id AND
                id!=$s_id AND
                root_id=id";
				$this->db->sql_exec($sql);

				$sql = "UPDATE {$tb} SET ordr={$t_order}+1 WHERE id={$s_id}";
				$this->db->sql_exec($sql);
			} 
		} 
		$this->_release_lock();
		return $s_id;
	} 
	
	/**
	* Callback for uasort used to sort siblings
	* 
	* @access private 
	*/
	function _sec_sort($node1, $node2)
	{ 
		// Within the same level?
		if ($node1['level'] != $node2['level'])
		{
			return strnatcmp($node1['l'], $node2['l']);
		} 
		// Are they siblings?
		$p1 = $this->get_parent($node1);
		$p2 = $this->get_parent($node2);
		if ($p1['id'] != $p2['id'])
		{
			return strnatcmp($node1['l'], $node2['l']);
		} 
		// Same field value? Use the lft value then
		$field = $this->_secondary_sort;
		if ($node1[$field] == $node2[$field])
		{
			return strnatcmp($node1['l'], $node2[l]);
		} 
		// Compare between siblings with different field value
		return strnatcmp($node1[$field], $node2[$field]);
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
			$tmp_field = $this->_node_table . '.' . $key . ' AS ' . $val;
			$sql_exec_fields[] = $tmp_field;
		} 

		$fields = implode(', ', $sql_exec_fields);
		return $fields;
	} 
	
	function _get_result_set($sql)
	{
		$this->db->sql_exec($sql);
		$nodes =& $this->db->get_array('id');

		return $nodes;
	} 
	
	function _assign_result_set(&$nodes, $sql)
	{
		$this->_sql = $sql;
		$this->db->sql_exec($sql);
		$this->db->assign_array($nodes, 'id');
	} 
		
	/**
	* This enables you to set specific options for each output method
	* 
	* @param constant $sort_mode 
	* @access public 
	* @return Current sort_mode
	*/
	function set_sort_mode($sort_mode)
	{
		if ($sort_mode && in_array($sort_mode, $this->_sort_modes))
		{
			$last_mode = $this->_sort_mode;
			$this->_sort_mode = $sort_mode;
			return $last_mode;
		} 
		else
			return $this->_sort_mode;
	} 
			
	function _set_lock($exclusive = false)
	{
		if(!$this->_structure_table_lock)
			$this->_lock_gc();

		if (!$lock_id = $this->_structure_table_lock)
		{
			$lock_id = $this->_structure_table_lock = uniqid('lck-');
			$sql = sprintf('INSERT INTO %s SET lock_id="%s", lock_table="%s", lock_stamp=%s',
											$this->_lock_table,
											$lock_id, 
											$this->_node_table,
											time());
		} 
		else
		{
			$sql = sprintf('UPDATE %s SET lock_stamp=%s WHERE lock_id="%s" AND lock_table="%s"',
											$this->_lock_table,
											time(),
											$lock_id, 
											$this->_node_table);
		} 

		if ($exclusive)
		{
			$this->_lock_exclusive = true;
		} 

		$this->db->sql_exec($sql);

		return $lock_id;
	} 
	
	function _release_lock($exclusive = false)
	{
		if ($exclusive)
		{
			$this->_lock_exclusive = false;
		} 

		if ((!$lock_id = $this->_structure_table_lock) || $this->_lock_exclusive)
		{
			return false;
		} 

		$sql = "DELETE FROM {$this->_lock_table}
            WHERE lock_table='{$this->_node_table}' AND
            lock_id='$lock_id'";

		$this->db->sql_exec($sql);

		$this->_structure_table_lock = false;
		
		return true;
	} 
	
	function _lock_gc()
	{
		$lock_ttl = (time() - $this->_lock_ttl);
		
		$sql = "DELETE FROM {$this->_lock_table}
            WHERE lock_table='{$this->_node_table}' AND
            lock_stamp < {$lock_ttl}";

		$this->db->sql_exec($sql);
	} 
	
	function _values2update_query($values, $insert_data = false)
	{
		if (is_array($insert_data))
		{
			$values = $values + $insert_data;
		} 

		$arq = array();
		foreach($values AS $key => $val)
		{
			$k = trim($key); 
			$iv = $this->db->escape(trim($val));
			$arq[] = "$k='$iv'";
		} 

		if (!is_array($arq) || count($arq) == 0)
		{
			return false;
		} 

		$sql_exec = implode(', ', $arq);
		return $sql_exec;
	} 
	
	function _values2insert_query($values, $insert_data = false)
	{
		if (is_array($insert_data))
		{
			$values = $values + $insert_data;
		} 

		$arq = array();
		foreach($values AS $key => $val)
		{
			$k = $key; 
			$iv = $this->db->escape(trim($val));
			$arq[$k] = "'$iv'";
		} 

		if (!is_array($arq) || count($arq) == 0)
		{
			return false;
		} 

		return $arq;
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
		{
			return true;
		} 
		foreach($values as $field => $value)
		{
			if (!isset($this->_params[$field]))
			{
	    	debug :: write_error(NESE_ERROR_WRONG_MPARAM,
	    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
	    		 array('param' => $field)
	    	);
				unset($values[$field]);
			} 
			else
			{
				if (in_array($this->_params[$field], $this->_required_params))
				{
		    	debug :: write_error(NESE_ERROR_WRONG_MPARAM,
		    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
	    		 array('param is autogenerated and can\'t be passed' => $field)
		    	);

					unset($values[$field]);
				} 
			} 
		} 
	} 	
} 

?>