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
require_once(LIMB_DIR . 'core/tree/drivers/nested_sets_driver.class.php');

class nested_sets_driver_test_version extends nested_sets_driver
{	
	var $_node_table = 'test_nested_tree1';
	var $_lock_ttl = 5;
	
	function nested_sets_driver_test_version()
	{
		parent :: nested_sets_driver();
	}
}

SimpleTestOptions::ignore('test_nested_sets_driver');

class test_nested_sets_driver extends UnitTestCase
{
	var $db = null;
	
  function test_nested_sets_driver() 
  {
  	parent :: UnitTestCase();
  	
 		$this->db = db_factory :: instance();
  }

	function setUp()
	{
		debug_mock :: init($this);
		
		$this->_tree = new nested_sets_driver_test_version();
	} 

	function tearDown()
	{
		debug_mock :: tally();
		
		$this->db->sql_delete('test_nested_tree1');		
	} 
	
	function _move_tree_across($branches, $mvt, $nodecount)
	{
		foreach($branches[0] as $nodeid => $node)
		{
			foreach($branches[1] as $tnodeid => $tnode)
			{
				if($nodeid == $tnodeid)
				{
					debug_mock :: expect_write_error(NESE_ERROR_RECURSION, 
						array('id' => $nodeid, 'target_id' => $tnodeid));
				}
				else
				{
					$current_source = $this->_tree->get_node($nodeid);
					$current_target = $this->_tree->get_node($tnodeid);
					
					if(	
							($current_target['root_id'] == $current_source['root_id']) &&
							(($current_source['l'] <= $current_target['l']) &&
							($current_source['r'] >= $current_target['r'])))
					{
						debug_mock :: expect_write_error(NESE_ERROR_RECURSION, 
							array('id' => $nodeid, 'target_id' => $tnodeid));
					}
				}
				
				if(($ret = $this->_tree->move_tree($nodeid, $tnodeid, $mvt)) === false)
					continue;
				
				$mnode = $this->_tree->get_node($ret);
				$this->assertEqual($ret, $nodeid, 'Nodeid was not returned as expected');
				$this->assertEqual($nodecount, count($this->_tree->get_all_nodes()), 'Node count changed');
				$p = $this->_tree->get_parent($nodeid);

				if ($mvt == NESE_MOVE_BELOW)
				{
					$this->assertEqual($tnode['id'], $p['id'], 'Move below failed (parent ID)');
				} 

				if ($mnode['id'] != $mnode['root_id'])
				{
					$this->assertEqual($p['id'], $mnode['parent_id'], 'Parent ID is wrong');
				} 
			} 
		} 
	} 

	function _delete_nodes($parent_id, $keep = false)
	{
		$children = $this->_tree->get_children($parent_id);
		$dc = 0;
		if (is_array($children))
		{
			$cct = count($children);
			$randval = $randval = mt_rand(0, $cct-1);
			foreach($children AS $cid => $child)
			{ 
				// Randomly delete some trees top down instead of deleting bottom up
				// and see if the result is still O.K.
				if ($dc == $randval)
				{
					$this->_tree->delete_node($cid);
					$this->assertFalse($this->_tree->get_node($cid), 'pick_node did not return false after node deletion.');
					continue;
				} 

				if ($child['r']-1 != $child['l'])
				{
					$this->_delete_nodes($cid);
				} 
				$currchild = $this->_tree->get_node($cid); 
				// The next remaining child in the tree should always have the order 1
				$this->assertEqual(1, $currchild['ordr'], 'Child has wrong order');

				$this->assertEqual($currchild['l'], $currchild['r']-1, 'Wrong lft-rgt checksum after child deletion.');
				$this->_tree->delete_node($cid);
				$this->assertFalse($this->_tree->get_node($cid), 'pickNode didn not return false after node deletion.');
				$dc++;
			} 
		} 
		elseif (!$keep)
		{
			$parent = $this->_tree->get_node($parent_id);
			$this->assertEqual($parent['l'], $parent['r']-1, 'Wrong lft-rgt checksum after child deletion.');
			$this->_tree->delete_node($parent_id);
			$this->assert_true($this->_tree->get_node($parent_id), 'pickNode didn not return false after node deletion.');
		} 
	} 

	function _setup_root_nodes($nbr)
	{
		$nodes = array();
		$lnid = false; 
		// Create some root_nodes
		
		for($i = 0; $i < $nbr; $i++)
		{
			$node_index = $i + 1;
			$values = array();
			$values['identifier'] = 'Node ' . $node_index;

			if ($i == 0)
			{
				$nid[$i] = $this->_tree->create_root_node($values, false, true);
			} 
			else
			{
				$nid[$i] = $this->_tree->create_root_node($values, $nid[$i-1]);
			} 
			
			$this->db->sql_select('test_nested_tree1', '*', 'root_id='. $nid[$i]);
			$new_root_node = $this->db->fetch_row();
			
			$this->assertEqual($new_root_node['root_id'], $nid[$i], "Root node $i: creation failed");
		} 
		
		$this->assertEqual($nbr, count($nid), "Root node creation went wrong.");
		return $nid;
	} 

	function _create_random_nodes($rnc, $nbr)
	{
		$root_nodes = $this->_create_root_nodes($rnc); 
		// Number of nodes to create
		$available_parents = array();
		$relation_tree = array();
		foreach($root_nodes AS $rid => $rootnode)
		{
			$available_parents[] = $rid;
		} 

		for($i = 0; $i < $nbr-1; $i++)
		{
			$randval = mt_rand(0, count($available_parents)-1);
			$choosemethod = mt_rand(1, 2);
			$target = $this->_tree->get_node($available_parents[$randval]);
			$nindex = $i;
			$values = array();
			$return_id = false;
			if ($choosemethod == 1)
			{
				$method = 'create_sub_node';
				$exp_target_lft_after = $target['l'];
				$exp_target_rgt_after = $target['r'] + 2;
				$values['identifier'] = $target['identifier'] . '.' . $nindex;
				$parent_id = $target['id'];
			} 
			else
			{
				$method = 'create_right_node';
				$return_id = true;

				if (isset($relation_tree[$target['id']]['parent_id']))
				{
					$parent_id = $relation_tree[$target['id']]['parent_id'];
					$parent = $this->_tree->get_node($parent_id);
					$exp_target_lft_after = $parent['l'];
					$exp_target_rgt_after = $parent['r'] + 2;
				} 
				else
				{
					$parent_id = $target['parent_id'];
				} 
				if (isset($relation_tree[$parent_id]['children']))
				{
					$cct = count($relation_tree[$parent_id]['children']) + 1 ;
				} 
				else
				{
					$cct = 1;
				} 

				if (!empty($parent))
				{
					$values['identifier'] = $parent['identifier'] . '.' . $cct;
				} 
				else
				{
					$root_nodes = $this->_tree->get_root_nodes(true);
					$cct = count($root_nodes) + 1;
					$values['identifier'] = 'Node ' . $cct;
				} 
			} 

			$available_parents[] = $nid = $this->_tree->$method($target['id'], $values);

			$target_after = false;
			if ($method == 'create_sub_node')
			{
				$target_after = $this->_tree->get_node($target['id']);
			} elseif ($parent_id)
			{
				$target_after = $this->_tree->get_node($parent['id']);
			} 

			if ($target_after)
			{
				$this->assertEqual($exp_target_lft_after, $target_after['l'], "Wrong LFT after $method");
				$this->assertEqual($exp_target_rgt_after, $target_after['r'], "Wrong RGT after $method");
			} 
			if ($choosemethod == 1)
			{ 
				// create_sub_node()
				$relation_tree[$nid]['parent_id'] = $parent_id;
				$relation_tree[$target['id']]['children'][] = $nid;
				$exp_rootid = $target['root_id'];
			} 
			else
			{ 
				// createRightNode()
				if ($parent_id)
				{
					$exp_rootid = $parent['root_id'];
				} 
				else
				{
					$exp_rootid = $nid;
				} 
				$relation_tree[$parent_id]['children'][] = $nid;
				$relation_tree[$nid]['parent_id'] = $parent_id;
			} 
			$cnode = $this->_tree->get_node($nid); 
			// Test rootid
			$this->assertEqual($exp_rootid, $cnode['root_id'], "Node {$cnode['identifier']}: Wrong root id.");
		} 

		$exp_cct = 0;
		$cct = 0; 
		// Traverse the tree and verify it using getChildren
		foreach($root_nodes AS $rid => $rootnode)
		{
			$rn = $this->_tree->get_node($rid);
			$cct = $cct + $this->_traverse_children($rn, $relation_tree); 
			// Calc the expected number of children from lft-rgt
			$exp_cct = $exp_cct + floor(($rn['r'] - $rn['l']) / 2);
		} 
		// Test if all created nodes got returned
		$this->assertEqual($exp_cct, $cct, 'Total node count returned is wrong');

		return $relation_tree;
	} 

	function _create_root_nodes($nbr, $dist = false)
	{ 
		// Creates 10 root_nodes
		$rplc = array();
		$nodes = $this->_setup_root_nodes($nbr);

		$disturbidx = false;
		$disturb = false;
		$disturb_set = false; 
		// Disturb the order by adding a node in the middle of the set
		if ($dist)
		{
			$values = array();
			$values['identifier'] = 'disturb'; 
			$disturbidx = count($nodes);
			$disturb = 6;
			$nodes[$disturbidx] = $this->_tree->create_root_node($values, $disturb);
		} 

		for($i = 0; $i < count($nodes); $i++)
		{
			$node[$nodes[$i]] = $this->_tree->get_node($nodes[$i]);

			$node_index = $i + 1;

			if (!empty($disturb) && $node_index - 1 == $disturb)
			{
				$disturb_set = true;
			} 

			if (!$disturb_set)
			{
				$exp_order = $node_index;
				$exp_name = 'Node ' . $node_index;
			} elseif ($i == $disturbidx)
			{
				$exp_order = $disturb + 1;
				$exp_name = 'disturb';
			} 
			else
			{
				$exp_order = $node_index + 1;
				$exp_name = 'Node ' . $node_index;
			} 
			// Test Array
			$this->assertEqual(is_array($node[$nodes[$i]]), "Rootnode $node_index: No array given."); 
			// Test NodeID==RootID
			$this->assertEqual($node[$nodes[$i]]['id'], $node[$nodes[$i]]['root_id'], "Rootnode $node_index: node_id/root_id not equal."); 
			// Test lft/rgt
			$this->assertEqual(1, $node[$nodes[$i]]['l'], "Rootnode $node_index: LFT has to be 1");
			$this->assertEqual(2, $node[$nodes[$i]]['r'], "Rootnode $node_index: RGT has to be 2"); 
			// Test order
			$this->assertEqual($exp_order, $node[$nodes[$i]]['ordr'], "Rootnode $node_index: Wrong order."); 
			// Test Level
			$this->assertEqual(1, $node[$nodes[$i]]['level'], "Rootnode $node_index: Wrong level."); 
			// Test Name
			$this->assertEqual($exp_name, $node[$nodes[$i]]['identifier'], "Rootnode $node_index: Wrong name.");
		} 
		return $node;
	} 

	function _create_sub_node($rnc, $depth, $npl)
	{
		$root_nodes = $this->_create_root_nodes($rnc);

		$init = true;
		foreach ($root_nodes as $id => $parent)
		{
			$relation_tree = $this->_recurs_create_sub_node($id, $npl, $parent['identifier'], 1, $depth, $init);
			$init = false;
		} 
		return $relation_tree;
	} 

	function _recurs_create_sub_node($pid, $npl, $pname, $currdepth, $maxdepth, $init = false)
	{
		static $relation_tree;
		if ($init)
		{
			$relation_tree = array();
		} 
		if ($currdepth > $maxdepth)
		{
			return $relation_tree;
		} 

		$newdepth = $currdepth + 1;
		for($i = 0; $i < $npl; $i++)
		{
			$nindex = $i + 1;
			$values = array();
			$values['identifier'] = 'object_' . $nindex; 
			// Try to overwrite the rootid which should be set inside the method
			// $values['STRID'] = -100;
			$npid = $this->_tree->create_sub_node($pid, $values);
			$relation_tree[$npid]['parent_id'] = $pid;
			$relation_tree[$pid]['children'][] = $npid; 
			// fetch just created node for validation
			$nnode = $this->_tree->get_node($npid); 
			// fetch parent of the new node to get lft/rgt values to verify
			$pnode = $this->_tree->get_node($pid);

			$plft = $pnode['l'];
			$prgt = $pnode['r']; 
			// Expected values
			$exp_order = $nindex;
			$exp_name = $values['identifier'];
			$exp_level = $currdepth + 1;
			$exp_lft = $prgt - 2;
			$exp_rgt = $prgt - 1;
			$exp_rootid = $pnode['root_id']; 
			// Test Array
			$this->assertEqual(is_array($nnode), "Node {$values['identifier']}: No array given."); 
			// Test rootid
			$this->assertEqual($exp_rootid, $nnode['root_id'], "Node {$values['identifier']}: Wrong rootid"); 
			// Test lft/rgt
			$this->assertEqual($exp_lft, $nnode['l'], "Node {$values['identifier']}: Wrong LFT");
			$this->assertEqual($exp_rgt, $nnode['r'], "Node {$values['identifier']}: Wrong RGT"); 
			// Test order
			$this->assertEqual($exp_order, $nnode['ordr'], "Node {$values['identifier']}: Wrong order."); 
			// Test Level
			$this->assertEqual($exp_level, $nnode['level'], "Node {$values['identifier']}: Wrong level."); 
			// Test Name
			$this->assertEqual($exp_name, $nnode['identifier'], "Node {$values['identifier']}: Wrong name."); 
			// Create new subnode
			$this->_recurs_create_sub_node($npid, $npl, $values['identifier'], $newdepth, $maxdepth);
		} 
		return $relation_tree;
	} 

	function _traverse_children($current_node, $relation_tree = array(), $reset = true)
	{
		static $occvals;

		if ($reset || !isset($occvals))
		{
			$occvals = array();
		} 

		$level = $current_node['level'];

		$children = $this->_tree->get_children($current_node['id']);

		if (!empty($relation_tree))
		{
			if (is_array($exp_children = $this->_traverse_child_relations($relation_tree, $current_node['id'], false, true)))
			{
				if (count($exp_children) == 0)
				{
					$exp_children = false;
				} 
				else
				{
					$exp_children = array_reverse($exp_children, true);
				} 
			} 
			// Test if the children fetched with API calls matches the children from the relationTree
			$this->assertEqual($exp_children, $children, "Node {$current_node['identifier']}: Children don't match children from relation tree.");
		} 

		$x = 0;
		$lcct = 0;

		if ($children)
		{
			$level++;
			foreach($children as $cid => $child)
			{ 
				// Test order
				$exp_order = $x + 1;
				$exp_level = $level;
				$exp_rootid = $current_node['root_id'];
				$this->assertEqual($exp_order, $child['ordr'], "Node {$current_node['identifier']}: Wrong order value."); 
				// Test rootid
				$this->assertEqual($exp_rootid, $child['root_id'], "Node {$current_node['identifier']}: Wrong root id."); 
				// Test level
				$this->assertEqual($exp_level, $child['level'], "Node {$current_node['identifier']}: Wrong level value.");
				$lcct = $lcct + $this->_traverse_children($child, $relation_tree, false);
				$x++;
			} 
		} 
		// Calc the expected total number of children
		// This is a nice general check if everything's worked as it should
		$exp_cct = floor(($current_node['r'] - $current_node['l']) / 2);
		$cct = $x + $lcct;

		$this->assertEqual($exp_cct, $cct, "Node {$current_node['identifier']}: Wrong childcount."); 
		// Test rgt
		$lft = $current_node['l'];
		$rgt = $current_node['r'];
		$exp_rgt = ($lft + ($cct * 2) + 1);
		$this->assertEqual($exp_rgt, $rgt, "Node {$current_node['identifier']}: Wrong RGT value."); 
		// Test if no lft/rgt values have been used twice
		$rootid = $current_node['root_id'];

		$this->assertFalse(isset($occvals[$lft]),
			"Node {$current_node['identifier']}: Uses already used LFT value."
			);

		$this->assertFalse(isset($occvals[$rgt]),
			"Node {$current_node['identifier']}: Uses already used RGT value."
			);

		$occvals[$lft] = $lft;
		$occvals[$rgt] = $rgt;
		return $cct;
	} 

	function _traverse_parent_relations($relation_tree, $nid, $init = false)
	{
		static $relation_nodes;
		if ($init)
		{
			$relation_nodes = array();
		} 

		if (empty($relation_tree[$nid]['parent_id']))
		{
			return $relation_nodes;
		} 
		$parent_id = $relation_tree[$nid]['parent_id'];
		$relation_nodes[$parent_id] = $this->_tree->get_node($parent_id);
		$this->_traverse_parent_relations($relation_tree, $parent_id);
		return $relation_nodes;
	} 

	function _traverse_child_relations($relation_tree, $nid, $deep = false, $init = false, $include_parent = false, $check_expanded = false)
	{
		static $relation_nodes;
		if ($init)
		{
			$relation_nodes = array();
			if($include_parent)
				$relation_nodes[$nid] = $this->_tree->get_node($nid);
			$init = false;
		} 

		if (empty($relation_tree[$nid]['children']))
		{
			return $relation_nodes;
		} 
		$children = $relation_tree[$nid]['children'];
		
		if($check_expanded)
		{
			if(!$this->_tree->is_node_expanded($nid))
			{
				return $relation_nodes;
			}
		}
		
		for($i = 0;$i < count($children);$i++)
		{
			$cid = $children[$i];
			
			$relation_nodes[$cid] = $this->_tree->get_node($cid);
						
			if ($deep)
			{	
				$this->_traverse_child_relations($relation_tree, $cid, $deep, $init, $include_parent, $check_expanded);
			} 
		} 
		return $relation_nodes;
	}
} 

?>