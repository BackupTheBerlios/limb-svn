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


require_once(TEST_CASES_DIR . '/tree/test_nested_tree.php');

class test_nested_set_creation extends test_nested_tree
{
	
	function test_nested_set_creation()
	{
		parent :: test_nested_tree();
	}
	
	/**
	* Simply create some rootnodes and see if this works
	*/
	function test_create_root_node($dist = false)
	{
		$this->_create_root_nodes(15);
	} 

	/**
	* Create some rootnodes and create another rootnodes inbetween the others to look
	* if the ordering is right afterwards
	*/
	function test_create_root_node_mixup()
	{
		$this->_create_root_nodes(15, true);
	} 

	/**
	* Recursively create a tree using createSubNode and verify the results
	*/
	function test_create_sub_node()
	{
		$rnc = 3;
		$depth = 3;
		$npl = 3;
		return $this->_create_sub_node($rnc, $depth, $npl);
	} 

	/**
	* Create some right nodes and query some meta informations
	*/
	function test_create_right_node()
	{
		$rnc = 6;
		$rootnodes = $this->_create_root_nodes($rnc);
		$x = 0;
		foreach($rootnodes AS $rid => $rootnode)
		{
			$values['identifier'] = 'R' . $x;
			$rn1 = $this->_tree->create_right_node($rid, $values);
			$values['identifier'] = 'RS' . $x;
			$sid = $this->_tree->create_sub_node($rn1, $values);
			$values['identifier'] = 'RSR' . $x; 
			// Try to overwrite the ROOTID which should be set inside the method
			// $values['ROOTID'] = -100;
			$rn2 = $this->_tree->create_right_node($sid, $values);
			$x++;

			$right1 = $this->_tree->get_node($rn1);
			$right2 = $this->_tree->get_node($rn2); 
			// Root ID has to equal ID
			$this->assertEqual($right1['root_id'], $right1['id'], "Right node has wrong root id."); 
			// Order
			$upd_rootnode = $this->_tree->get_node($rid);

			$this->assertEqual($upd_rootnode['ordr'] + 1, $right1['ordr'], "Right node has wrong order."); 
			// Level
			$this->assertEqual(1, $right1['level'], "Right node has wrong level."); 
			// Children
			$exp_cct = floor(($right1['r'] - $right1['l']) / 2);
			$allchildren = $this->_tree->get_sub_branch($rn1); 
			// This is also a good test if l/r values are ok
			$this->assertEqual($exp_cct, count($allchildren), "Right node has wrong child count."); 
			// Order
			$upd_subnode = $this->_tree->get_node($sid);
			$this->assertEqual($upd_subnode['ordr'] + 1, $right2['ordr'], "Right node has wrong order."); 
			// Level
			$this->assertEqual(2, $right2['level'], "Right node has wrong level."); 
			// Test root id
			$this->assertEqual($right1['root_id'], $right2['root_id'], "Right node has wrong root id.");
		} 
		$allnodes = $this->_tree->get_all_nodes();
		$this->assertEqual($rnc * 4, count($allnodes), "Wrong node count after right insertion");
		return true;
	} 

	/**
	* Create some left nodes and query some meta informations
	*/
	function test_create_left_node()
	{
		$rnc = 6;
		$rootnodes = $this->_create_root_nodes($rnc);
		$x = 0;
		foreach($rootnodes AS $rid => $rootnode)
		{
			$values['identifier'] = 'R' . $x;
			$rn1 = $this->_tree->create_left_node($rid, $values);
			$values['identifier'] = 'RS' . $x;
			$sid = $this->_tree->create_sub_node($rn1, $values);
			$values['identifier'] = 'RSR' . $x; 
			// Try to overwrite the ROOTID which should be set inside the method
			// $values['ROOTID'] = -100;
			$rn2 = $this->_tree->create_left_node($sid, $values);
			$x++;

			$left1 = $this->_tree->get_node($rn1);
			$left2 = $this->_tree->get_node($rn2); 
			// Root ID has to equal ID
			$this->assertEqual($left1['root_id'], $left1['id'], "Left node has wrong root id."); 
			// Order
			$upd_rootnode = $this->_tree->get_node($rid);
			$this->assertEqual($upd_rootnode['ordr']-1, $left1['ordr'], "Left node 1 has wrong order."); 
			// Level
			$this->assertEqual(1, $left1['level'], "Left  node has wrong level."); 
			// Children
			$exp_cct = floor(($left1['r'] - $left1['l']) / 2);
			$allchildren = $this->_tree->get_sub_branch($rn1); 
			// This is also a good test if l/r values are ok
			$this->assertEqual($exp_cct, count($allchildren), "Left  node has wrong child count."); 
			// Order
			$upd_subnode = $this->_tree->get_node($sid);
			$this->assertEqual($upd_subnode['ordr']-1, $left2['ordr'], "Left node 2 has wrong order."); 
			// Level
			$this->assertEqual(2, $left2['level'], "Left  node has wrong level."); 
			// Test root id
			$this->assertEqual($left1['root_id'], $left2['root_id'], "Left  node has wrong root id.");
		} 
		$allnodes = $this->_tree->get_all_nodes();
		$this->assertEqual($rnc * 4, count($allnodes), "Wrong node count after right insertion");
		return true;
	} 

	/**
	* Create some rootnodes and randomly call createSubNode() or createRightNode()
	* on the growing tree. This creates a very random structure which
	* is intended to be a real life simulation to catch bugs not beeing
	* catched by the other tests.
	* Some basic regression tests including _traverseChildren() with a relation tree
	* are made.
	*/
	function test_create_nodes_random()
	{
		$this->_create_random_nodes(2, 20);
		return true;
	} 
} 

?>