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

class test_nested_sets_driver_query extends test_nested_sets_driver
{
	function test_nested_sets_driver_query()
	{
		parent :: test_nested_sets_driver();
	} 
	
	function setUp()
	{
		parent::setUp();
		
		debug_mock :: init($this);
	}
	
	function tearDown()
	{
		parent::tearDown();
		
		debug_mock :: tally();
	}
	
	/**
	* Creates some nodes and verifies the result
	*/
	function test_get_all_nodes()
	{
		$rnc = 1;
		$depth = 2;
		$npl = 3;
		$this->_create_sub_node($rnc, $depth, $npl);

		$allnodes = $this->_tree->get_all_nodes();
		$rootnodes = $this->_tree->get_root_nodes();
		$exp_cct = 0;
		foreach($rootnodes AS $rid => $rootnode)
		{
			$exp_cct = $exp_cct + floor(($rootnode['r'] - $rootnode['l']) / 2);
		} 
		// Does it really return all nodes?
		$cct = count($allnodes);
		$exp_cct = $exp_cct + count($rootnodes);
		$this->assertEqual($exp_cct, $cct, 'Total node count returned is wrong'); 
		// Verify the result agains pickNode()
		foreach($allnodes AS $nid => $node)
		{
			$this->assertEqual($this->_tree->get_node($nid), $node, 'Result differs from pickNode()');
		} 

		return true;
	} 

	/**
	* 
	* Create 2 sets of rootnodes (ordered and mixed) and see if the result matches
	* getRootNodes()
	* 
	*/
	function test_get_root_nodes()
	{ 
		// Create a simple set of rootnodes
		$rootnodes_exp = $this->_create_root_nodes(15);
		$rootnodes = $this->_tree->get_root_nodes();
		$this->assertEqual($rootnodes_exp, $rootnodes, 'getRootNodes() failed'); 
		// Create a mixed order set of rootnodes
		$rootnodes_exp = $this->_create_root_nodes(15, true);
		$rootnodes = $this->_tree->get_root_nodes();
		$this->assertEqual($rootnodes_exp, $rootnodes, 'getRootNodes() failed on mixed set');
		return true;
	} 

	/**
	* 
	* Handcraft the parent tree using the relation tree from _createSubNode()
	* and compare it against getParents()
	* 
	*/
	function test_get_parents()
	{
		$rnc = 1;
		$depth = 2;
		$npl = 3; 
		// Create a new tree
		$relation_tree = $this->_create_sub_node($rnc, $depth, $npl);
		$allnodes = $this->_tree->get_all_nodes(); 
		// Walk trough all nodes and compare it's relations whith the one provided
		// by the relation tree
		foreach($allnodes AS $nid => $node)
		{
			$parents = $this->_tree->get_parents($nid);
			$exp_parents = array_reverse($this->_traverse_parent_relations($relation_tree, $nid, true), true);
			$this->assertEqual($exp_parents, $parents, 'Differs from relation traversal result.');
		} 
		return true;
	} 

	/**
	* 
	* Build a simple tree run getParent() and compare it with the relation tree
	* 
	*/
	function test_get_parent()
	{
		$rnc = 1;
		$depth = 2;
		$npl = 3; 
		// Create a new tree
		$relation_tree = $this->_create_sub_node($rnc, $depth, $npl);
		$allnodes = $this->_tree->get_all_nodes(); 
		// Walk trough all nodes and compare it's relations whith the one provided
		// by the relation tree
		foreach($allnodes AS $nid => $node)
		{
			$parent = $this->_tree->get_parent($nid, true);
			if (!isset($relation_tree[$nid]['parent_id']))
			{
				$this->assertFalse($parent, 'A rootnode returned a parent');
				continue;
			} 
			$this->assertEqual($relation_tree[$nid]['parent_id'], $parent['id'], 'Relation tree parent doesn\'t match method return');
		} 
		return true;
	} 

	function test_get_siblings()
	{
		$rnc = 1;
		$depth = 2;
		$npl = 3; 
		// Create a new tree
		$relation_tree = $this->_create_sub_node($rnc, $depth, $npl);
		$allnodes = $this->_tree->get_all_nodes(); 
		// Walk trough all nodes and compare it's relations whith the one provided
		// by the relation tree
		foreach($allnodes AS $nid => $node)
		{
			if (!$children = $this->_tree->get_children($nid))
			{
				continue;
			} 
			foreach($children AS $cid => $child)
			{
				$siblings = $this->_tree->get_siblings($cid);
				$this->assertEqual($children, $siblings, 'Children don\'t match getSiblings()');
			} 
		} 
		return true;
	} 

	/**
	* 
	* Create some children
	* The dirty work is done in _traverse_children()
	* Here we only calc if the expected number of children returned matches
	* the count of getChildren()
	* 
	*/
	function test_get_children()
	{
		$rnc = 1;
		$depth = 2;
		$npl = 3; 
		// Just see if empty nodes are recognized
		$nids = $this->_setup_root_nodes(3);
		foreach($nids AS $rix => $nid)
		{
			$this->assertFalse($this->_tree->get_children($nid), 'getChildren returned value for empty rootnode');
		} 
		// Now build a little tree to test
		$relation_tree = $this->_create_sub_node($rnc, $depth, $npl);

		$rootnodes = $this->_tree->get_root_nodes();
		$exp_cct = 0;
		$cct = 0;
		foreach($rootnodes AS $rid => $rootnode)
		{ 
			// Traverse the tree and verify it against the relationTree
			$cct = $cct + $this->_traverse_children($rootnode, $relation_tree, true); 
			// Calc the expected number of children from lft-rgt
			$exp_cct = $exp_cct + floor(($rootnode['r'] - $rootnode['l']) / 2);
		} 
		// Test if all created nodes got returned
		$this->assertEqual($exp_cct, $cct, 'Total node count returned is wrong');
		return true;
	} 

	/**
	* 
	* If we only have one branch getAllNodes() has to equal getBranch()
	* 
	*/
	function test_get_branch()
	{
		$rnc = 1;
		$depth = 2;
		$npl = 3; 
		// Create a new tree
		$this->_create_sub_node($rnc, $depth, $npl);
		$allnodes = $this->_tree->get_all_nodes();
		$branch = $this->_tree->get_branch($npl, true);
		$this->assertEqual($allnodes, $branch, 'Result differs from getAllNodes()');
	} 

	/**
	* 
	* Handcraft a sub branch using the relation tree from _createSubNode()
	* and compare it against getSubBranch()
	* 
	*/
	function test_get_sub_branch()
	{
		$rnc = 1;
		$depth = 2;
		$npl = 3; 
		// Create a new tree
		$relation_tree = $this->_create_sub_node($rnc, $depth, $npl);
		//$allnodes = $this->_tree->get_all_nodes();
		foreach($relation_tree AS $nid => $relations)
		{
			$subbranch = $this->_tree->get_sub_branch($nid);
			$exp_sub_branch = $this->_traverse_child_relations($relation_tree, $nid, true, true);
			$this->assertEqual($subbranch, $exp_sub_branch, 'Differs from relation traversal result.');
		} 
		return true;
	} 

	/**
	* 
	* Create some rootnodes and run pickNode() on it.
	* 
	*/
	function test_get_node()
	{ 
		// Set some rootnodes
		$nids = $this->_setup_root_nodes(3); 
		// Loop trough the node id's of the newly created rootnodes
		for($i = 0; $i < count($nids); $i++)
		{
			$nid = $nids[$i];

			$nname = 'Node ' . $nid;
			$norder = $nid; 
			// Pick the current node and do the tests
			$nnode = $this->_tree->get_node($nid); 
			// Test Array
			$this->assertEqual(is_array($nnode), "Node $nname: No array given."); 
			// Test lft/rgt
			$this->assertEqual(1, $nnode['l'], "Node $nname: Wrong LFT");
			$this->assertEqual(2, $nnode['r'], "Node $nname: Wrong RGT"); 
			// Test order
			$this->assertEqual($norder, $nnode['ordr'], "Node $nname: Wrong order."); 
			// Test Level
			$this->assertEqual(1, $nnode['level'], "Node $nname: Wrong level."); 
			// Test Name
			$this->assertEqual($nname, $nnode['identifier'], "Node $nname: Wrong name.");
		} 
		return true;
	} 
	
	function test_get_node_by_path()
	{
		$rnc = 1;
		$depth = 2;
		$npl = 3; 
		// Create a new tree
		$relation_tree = $this->_create_sub_node($rnc, $depth, $npl);
		
		$paths = array();
		
		$this->_create_identifier_paths(array_keys($relation_tree), $relation_tree, $paths);
		
		$counter = 0;
		foreach($paths as $id => $path)
		{
			$node = $this->_tree->get_node_by_path($path . (($counter++ % 2) ? '/' : ''));
			$this->assertNotIdentical($node, false);
			$this->assertEqual($id, $node['id']);
		}
		
		$this->assertIdentical($this->_tree->get_node_by_path('/no/such/a/branch'), false);
		$this->assertIdentical($this->_tree->get_node_by_path('/'), false);
	}
	
	function test_get_sub_branch_by_path()
	{
		$rnc = 1;
		$depth = 2;
		$npl = 3; 
		// Create a new tree
		$relation_tree = $this->_create_sub_node($rnc, $depth, $npl);

		$paths = array();
		
		$this->_create_identifier_paths(array_keys($relation_tree), $relation_tree, $paths);
		
		foreach($paths as $id => $path)
		{
			$include_parent = mt_rand(0, 1) ? true : false;
			
			$nodes = $this->_tree->get_sub_branch_by_path($path, -1, $include_parent);

			$exp_sub_branch = $this->_traverse_child_relations($relation_tree, $id, true, true, $include_parent);
			
			$this->assertEqual($nodes, $exp_sub_branch, 'Differs from relation traversal result.');			
		}		
	}

	function test_get_sub_branch_by_path_check_expanded_parents()
	{
		$rnc = 1;
		$depth = 2;
		$npl = 3; 
		// Create a new tree
		$relation_tree = $this->_create_sub_node($rnc, $depth, $npl);
		
		$paths = array();
		
		$nodes_ids = array_keys($relation_tree);
		
		$this->_create_identifier_paths($nodes_ids, $relation_tree, $paths);
		
		$this->_tree->reset_expanded_parents();
		
		foreach($nodes_ids as $id)
		{
			$i = mt_rand(0, sizeof($nodes_ids)-1);
			
			$method = mt_rand(0, 2);
			
			if($method == 0)
				$this->assertTrue($this->_tree->collapse_node((int)$nodes_ids[$i]));
			elseif($method == 1)
				$this->assertTrue($this->_tree->expand_node((int)$nodes_ids[$i]));
			else
				$this->assertTrue($this->_tree->toggle_node((int)$nodes_ids[$i]));
		}
		
		foreach($paths as $id => $path)
		{
			$include_parent = mt_rand(0, 1) ? true : false;
			
			$exp_sub_branch = $this->_traverse_child_relations($relation_tree, $id, true, true, $include_parent, true);
			
			$nodes = $this->_tree->get_sub_branch_by_path($path, -1, $include_parent, true);
			
			$this->assertEqual($nodes, $exp_sub_branch, 'Differs from relation traversal result.');			
		}		
	}
	
	function test_change_order()
	{
		$rnc = 1;
		$depth = 2;
		$npl = 3; 
		// Create a new tree
		$relation_tree = $this->_create_sub_node($rnc, $depth, $npl);
		
		debug_mock :: expect_write_error('node not found', array('node_id' => 0));
		$this->assertFalse($this->_tree->change_node_order(0, 'up'));
		
		$this->assertFalse($this->_tree->change_node_order(1, 'up'));
		$this->assertFalse($this->_tree->change_node_order(1, 'down'));
		
		foreach(array_keys($relation_tree) as $id)
		{
			if(isset($relation_tree[$id]['children']))
			{
				foreach($relation_tree[$id]['children'] as $child_id)
				{
					$children_before = $this->_tree->get_children($id);
					
					$direction = mt_rand(0,1) ? 'up' : 'down';
					
					$result = $this->_tree->change_node_order($child_id, $direction);
					
					$children_after = $this->_tree->get_children($id);
					
					$pos_before = array_search($child_id, array_keys($children_before));
					
					$pos_after = array_search($child_id, array_keys($children_after));
					
					if($direction == 'up')
					{
						if($pos_before == 0)
						{
							$this->assertFalse($result);
						}
						else
						{
							$this->assertTrue($result);
							$this->assertEqual(($pos_after - $pos_before), -1);
						}	
					}
					elseif($direction == 'down')
					{
						if($pos_before == (sizeof($children_before) - 1))
						{
							$this->assertFalse($result);
						}
						else
						{
							$this->assertTrue($result);
							$this->assertEqual(($pos_after - $pos_before), 1);
						}
					}
				}
			}
		}
	}
	
	function test_get_max_child_identifier()
	{
		$values['identifier'] = 'root';
		$values['object_id'] = 1;
		
		$parent_id = $this->_tree->create_root_node($values, false, true);
		
		$this->assertEqual($this->_tree->get_max_child_identifier($parent_id), '');

		$values['identifier'] = 290;
		$values['object_id'] = 2;		
		$this->_tree->create_sub_node($parent_id, $values);
		
		$this->assertEqual($this->_tree->get_max_child_identifier($parent_id), 290);

		$values['identifier'] = 'wow';
		$values['object_id'] = 3;		
		$this->_tree->create_sub_node($parent_id, $values);
		
		$this->assertEqual($this->_tree->get_max_child_identifier($parent_id), 'wow');

		$values['identifier'] = 560;
		$values['object_id'] = 4;		
		$this->_tree->create_sub_node($parent_id, $values);
		
		$this->assertEqual($this->_tree->get_max_child_identifier($parent_id), 'wow');

		$values['identifier'] = 'wow3';
		$values['object_id'] = 5;		
		$this->_tree->create_sub_node($parent_id, $values);
		
		$this->assertEqual($this->_tree->get_max_child_identifier($parent_id), 'wow3');
	}
		
	function _create_identifier_paths($ids, $relation_tree, &$result, $path='')
	{
		sort($ids);
		foreach($ids as $id)
		{
			$node = $this->_tree->get_node($id);
			
			if(!isset($result[$id]))
				$result[$id] = $path . '/'. $node['identifier'];
			
			if(isset($relation_tree[$id]['children']))
			{
				$this->_create_identifier_paths($relation_tree[$id]['children'], $relation_tree, $result, $result[$id]);
			}
		}
	}
} 

?>