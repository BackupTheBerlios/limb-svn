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

class test_nested_sets_driver_manipulation extends test_nested_sets_driver
{
	function test_nested_sets_driver_manipulation()
	{
		parent :: test_nested_sets_driver();
	} 

	/**
	* Creates a tree and recursively deletes nodes doing regression tests on
	* the remaining nodes
	*/
	function test_delete_node()
	{
		$relation_tree = $this->_create_random_nodes(2, 20);
		$rootnodes = $this->_tree->get_root_nodes();

		foreach($rootnodes AS $rid => $rootnode)
		{
			$this->_delete_nodes($rid, true);
			$rn = $this->_tree->get_node($rid);
			$this->assertEqual(1, $rn['l'], 'Wrong LFT value');
			$this->assertEqual(2, $rn['r'], 'Wrong RGT value');
		} 
		return true;
	} 


	/**
	* 
	* Creates some nodes and tries to update them
	* 
	*/
	function test_update_node()
	{
		$rootnodes = $this->_create_root_nodes(3);
		$x = 0;
		foreach($rootnodes AS $rid => $node)
		{
			$values['identifier'] = 'U' . $x; 
			// $values['ROOTID'] = -100;
			$this->_tree->update_node($rid, $values);
			$rn = $this->_tree->get_node($rid);
			$this->assertEqual('U' . $x, $rn['identifier'], 'Nodename update failed');
			$this->assertEqual($node['root_id'], $rn['root_id'], 'Rootid was overwritten');
			$x++;
		} 
		return true;
	} 

	function test_root_under_root()
	{
		$rootnodes = $this->_create_root_nodes(3);

		$ret = $this->_tree->move_tree($rootnodes[1]['id'], $rootnodes[2]['id'], NESE_MOVE_BELOW);

		$source = $this->_tree->get_node($rootnodes[1]['id']);
		$parent = $this->_tree->get_parent($rootnodes[1]['id']);
		$target = $this->_tree->get_node($rootnodes[2]['id']);
		$this->assertEqual($target['id'], $source['parent_id'], 'Parent id from column is wrong');
		$this->assertEqual($target['id'], $parent['id'], 'Calculated parent id is wrong');
		return true;
	} 

	function test_move_tree()
	{ 
		$movemodes[] = NESE_MOVE_BEFORE;
		$movemodes[] = NESE_MOVE_AFTER;
		$movemodes[] = NESE_MOVE_BELOW;
		for($j = 0;$j < count($movemodes);$j++)
		{
			$mvt = $movemodes[$j]; 
			// Build a nice random tree
			$rnc = 1;
			$depth = 2;
			$npl = 2;
			$relation_tree = $this->_create_sub_node($rnc, $depth, $npl);

			$lastrid = false;
			$rootnodes = $this->_tree->get_root_nodes();
			$branches = array();
			$allnodes1 = $this->_tree->get_all_nodes();
			foreach($rootnodes AS $rid => $rootnode)
			{
				if ($lastrid)
				{	
					if($rid == $lastrid)
						debug_mock :: expect_write_error(NESE_ERROR_RECURSION);
					
					$this->_tree->move_tree($rid, $lastrid, $mvt);
				} 

				$branch = $this->_tree->get_branch($rid);
				if (!empty($branch))
				{
					$branches[] = $branch;
				} 

				if (count($branches) == 2)
				{
					$this->_move_tree_across($branches, $mvt, count($this->_tree->get_all_nodes()));
					$branches = array();
				} 
				$lastrid = $rid;
			} 

			$allnodes2 = $this->_tree->get_all_nodes(); 
			// Just make sure that all the nodes are still there
			$this->assertFalse(count(array_diff(array_keys($allnodes1), array_keys($allnodes2))), 'Nodes got lost during the move');
		} 
		return true;
	} 

	function test_copy_tree()
	{
		$values['identifier'] = 'Root1';
		$root1 = $this->_tree->create_root_node($values, false, true);

		$values['identifier'] = 'Root2';
		$root2 = $this->_tree->create_right_node($root1, $values);
		$values['identifier'] = 'Sub2-1';
		$sub2_1 = $this->_tree->create_sub_node($root2, $values);

		$values['identifier'] = 'Root2';
		$root3 = $this->_tree->create_right_node($root2, $values);
		$values['identifier'] = 'Sub3-1';
		$sub3_1 = $this->_tree->create_sub_node($root3, $values); 
		// Copy a Rootnode
		$root2_copy = $this->_tree->move_tree($root2, $root1, NESE_MOVE_BEFORE, true);
		$this->assertFalse($root2_copy == $root2, 'Copy returned wrong node id');

		$nroot2_copy = $this->_tree->get_node($root2_copy);
		$this->assertEqual($root2_copy, $nroot2_copy['id'], 'Copy created wrong node array'); 
		// Copy another Rootnode
		$root2_copy = $this->_tree->move_tree($root2, $root1, NESE_MOVE_AFTER, true);
		$this->assertFalse($root2_copy == $root2, 'Copy returned wrong node id');

		$nroot2_copy = $this->_tree->get_node($root2_copy);
		$this->assertEqual($root2_copy, $nroot2_copy['id'], 'Copy created wrong node array'); 
		// Copy tree below another Rootnode
		$root2_copy = $this->_tree->move_tree($root2, $root1, NESE_MOVE_BELOW, true);
		$this->assertFalse($root2_copy == $root2, 'Copy returned wrong node id');

		$nroot2_copy = $this->_tree->get_node($root2_copy);
		$this->assertEqual($root2_copy, $nroot2_copy['id'], 'Copy created wrong node array'); 
		// Copy subtree below another Rootnode
		$sub3_1_copy = $this->_tree->move_tree($sub3_1, $root1, NESE_MOVE_BELOW, true);
		$this->assertFalse($sub3_1_copy == $sub3_1, 'Copy returned wrong node id');

		$nsub3_1_copy = $this->_tree->get_node($sub3_1_copy);
		$this->assertEqual($sub3_1_copy, $nsub3_1_copy['id'], 'Copy created wrong node array');
	}
} 

?>