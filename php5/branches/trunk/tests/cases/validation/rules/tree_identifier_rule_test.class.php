<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(dirname(__FILE__) . '/single_field_rule_test.class.php');
require_once(LIMB_DIR . 'class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'class/core/tree/tree.class.php');
require_once(LIMB_DIR . 'class/core/dataspace.class.php');
require_once(LIMB_DIR . 'class/validators/rules/tree_identifier_rule.class.php');

class tree_identifier_rule_test extends single_field_rule_test
{
	var $db = null;
	var $node_id_root;
	var $node_id_ru;
	var $node_id_document;
	var $node_id_doc1;
	var $node_id_doc2;
		
	function setUp()
	{
		parent :: setUp();
		
		$this->db =& db_factory :: instance();
		
  	$tree =& tree :: instance();

		$values['identifier'] = 'root';
		$this->node_id_root = $tree->create_root_node($values, false, true);

		$values['identifier'] = 'ru';
		$values['object_id'] = 1;
		$this->node_id_ru = $tree->create_sub_node($this->node_id_root, $values);

		$values['identifier'] = 'document';
		$values['object_id'] = 10;
		$this->node_id_document = $tree->create_sub_node($this->node_id_ru, $values);

		$values['identifier'] = 'doc1';
		$values['object_id'] = 20;
		$this->node_id_doc1 = $tree->create_sub_node($this->node_id_ru, $values);

		$values['identifier'] = 'doc2';
		$values['object_id'] = 30;
		$this->node_id_doc2 = $tree->create_sub_node($this->node_id_ru, $values);		
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
	
	function test_tree_identifier_rule_blank()
	{
		$this->validator->add_rule(new tree_identifier_rule('test', $this->node_id_ru, $this->node_id_document));

		$data =& new dataspace();
		$data->set('test', '');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}

	function test_tree_identifier_rule_normal()
	{
		$this->validator->add_rule(new tree_identifier_rule('test', $this->node_id_ru, $this->node_id_document));

		$data =& new dataspace();
		$data->set('test', 'id_test');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}

	function test_tree_identifier_rule_error()
	{
		$this->validator->add_rule(new tree_identifier_rule('test', $this->node_id_ru, $this->node_id_document));

		$data =& new dataspace();
		$data->set('test', 'doc1');

		$this->error_list->expectOnce('add_error', array('test', strings :: get('error_duplicate_tree_identifier', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
	
	function test_tree_identifier_same_node_changed_identifier()
	{
		$this->validator->add_rule(new tree_identifier_rule('test', $this->node_id_ru, $this->node_id_doc1));

		$data =& new dataspace();
		$data->set('test', 'doc1');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}
	
	function test_tree_identifier_node_id_not_set_error()
	{
		$this->validator->add_rule(new tree_identifier_rule('test', $this->node_id_ru));

		$data =& new dataspace();
		$data->set('test', 'doc1');

		$this->error_list->expectOnce('add_error', array('test', strings :: get('error_duplicate_tree_identifier', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
} 

?>