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
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/tree/limb_tree.class.php');
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/tree_identifier_rule.class.php');

class test_tree_identifier_rule extends test_single_field_rule
{
	var $db = null;
	var $root_node_id;
	var $sub_node_id;
	
	function test_tree_identifier_rule()
	{
		$this->db =& db_factory :: instance();
		
		parent::UnitTestCase();
	} 
	
	function setUp()
	{
		parent :: setUp();
		
  	$tree =& limb_tree :: instance();

		$values['identifier'] = 'root';
		$this->root_node_id = $tree->create_root_node($values, false, true);

		$values['identifier'] = 'ru';
		$values['object_id'] = 1;
		$this->sub_node_id= $tree->create_sub_node($this->root_node_id, $values);

		$values['identifier'] = 'document';
		$values['object_id'] = 10;
		$tree->create_sub_node($this->sub_node_id, $values);

		$values['identifier'] = 'doc1';
		$values['object_id'] = 20;
		$tree->create_sub_node($this->sub_node_id, $values);

		$values['identifier'] = 'doc2';
		$values['object_id'] = 30;
		$tree->create_sub_node($this->sub_node_id, $values);		
	}
	
  function tearDown()
  { 
  	parent :: tearDown();
  	$this->_clean_up();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object_tree');
  }

	function test_tree_identifier_rule_empty()
	{
		$this->validator->add_rule(new tree_identifier_rule('test', $this->sub_node_id));

		$data =& new dataspace();
		$data->set('test', 'test_id');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}
	
	function test_tree_identifier_rule_blank()
	{
		$this->validator->add_rule(new tree_identifier_rule('test', $this->sub_node_id));

		$data =& new dataspace();
		$data->set('test', '');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}

	function test_tree_identifier_rule_normal()
	{
		$this->validator->add_rule(new tree_identifier_rule('test', $this->sub_node_id));

		$data =& new dataspace();
		$data->set('test', 'test_id');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}

	function test_tree_identifier_rule_error()
	{
		$this->validator->add_rule(new tree_identifier_rule('test', $this->sub_node_id));

		$data =& new dataspace();
		$data->set('test', 'doc1');

		$this->error_list->expectOnce('add_error', array('test', 'DUPLICATE_TREE_IDENTIFIER', array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
	
	function test_tree_identifier_rule_normal_edit()
	{
		$this->validator->add_rule(new tree_identifier_rule('test', $this->sub_node_id, 'document'));

		$data =& new dataspace();
		$data->set('test', 'document');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}

} 

?>