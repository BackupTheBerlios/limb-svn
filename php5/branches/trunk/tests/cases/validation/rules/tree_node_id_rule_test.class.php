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
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/core/tree/tree.class.php');
require_once(LIMB_DIR . '/class/core/dataspace.class.php');
require_once(LIMB_DIR . '/class/validators/rules/tree_node_id_rule.class.php');

class tree_node_id_rule_test extends single_field_rule_test
{
	var $db = null;
	var $node_id_root;
	var $node_id_document;
		
	function setUp()
	{
		parent :: setUp();
		
		$this->db =& db_factory :: instance();
		
  	$tree = Limb :: toolkit()->getTree();

		$values['identifier'] = 'root';
		$this->node_id_root = $tree->create_root_node($values);
    
		$values['identifier'] = 'document';
		$values['object_id'] = 10;
		$this->node_id_document = $tree->create_sub_node($this->node_id_root, $values);
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
	
	function test_tree_node_id_rule_blank()
	{
		$this->validator->add_rule(new tree_node_id_rule('test'));

		$data = new dataspace();
		$data->set('test', '');

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}

	function test_tree_node_id_rule_false()
	{
		$this->validator->add_rule(new tree_node_id_rule('test'));

		$data = new dataspace();
		$data->set('test', false);

		$this->error_list->expectOnce('add_error', array('test', strings :: get('error_invalid_tree_node_id', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
		
	function test_tree_node_id_rule_normal()
	{
		$this->validator->add_rule(new tree_node_id_rule('test'));

		$data = new dataspace();
		$data->set('test', $this->node_id_document);

		$this->error_list->expectNever('add_error');

		$this->validator->validate($data);
		$this->assertTrue($this->validator->is_valid());
	}

	function test_tree_node_id_rule_error()
	{
		$this->validator->add_rule(new tree_node_id_rule('test'));

		$data = new dataspace();
		$data->set('test', -10000);

		$this->error_list->expectOnce('add_error', array('test', strings :: get('error_invalid_tree_node_id', 'error'), array()));

		$this->validator->validate($data);
		$this->assertFalse($this->validator->is_valid());
	}
} 

?>