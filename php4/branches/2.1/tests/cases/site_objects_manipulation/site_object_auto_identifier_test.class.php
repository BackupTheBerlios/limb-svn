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
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');
require_once(LIMB_DIR . 'core/model/site_object_factory.class.php');

class site_object_auto_identifier_test_version extends site_object
{		
	function _define_attributes_definition()
	{
		return complex_array :: array_merge(
				parent :: _define_attributes_definition(),
				array(
				'title' => '',
				'name' => array('type' => 'numeric'),
				'search' => array('search' => true),
				));		
	}
	
	function _define_class_properties()
	{
		return array(
			'ordr' => 1,
			'can_be_parent' => 1,
			'db_table_name' => 'site_object',
			'auto_identifier' => true
		);
	}
}

class site_object_auto_identifier_test extends UnitTestCase 
{ 
	var $db = null;
	var $object = null;
	
	var $parent_node_id = '';
		 	
  function setUp()
  {
  	$this->db =& db_factory :: instance();
  	
  	$this->_clean_up();
  	
  	$this->object = new site_object_auto_identifier_test_version();
  	
  	$tree =& tree :: instance();
  	
		$values['identifier'] = 'root';
		$values['object_id'] = 1;
		$this->parent_node_id = $tree->create_root_node($values, false, true);
		
		$class_id = $this->object->get_class_id();
		$this->db->sql_insert('sys_site_object', 
			array('id' => 1, 'class_id' => $class_id, 'current_version' => 1));

  	debug_mock :: init($this);
  }
  
  function tearDown()
  { 
  	$this->_clean_up();
  	
  	debug_mock :: tally();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
  	$this->db->sql_delete('sys_site_object_tree');
  	$this->db->sql_delete('sys_class');
  }
  	
  function test_create_first()
  {
  	debug_mock :: expect_never_write();

  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('node_test');
		
  	$id = $this->object->create();
  	
  	$this->assertEqual($this->object->get_identifier(), 1);
  }

  function test_create_text_only()
  {
  	debug_mock :: expect_never_write();

		$this->_create_node('ru');
		
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('node_test');
		
  	$id = $this->object->create();
  	
  	$this->assertEqual($this->object->get_identifier(), 'ru1');
  }

  function test_create_complex()
  {
  	debug_mock :: expect_never_write();

		$this->_create_node('10ru1');
		$this->_create_node('10ru2');
		$this->_create_node('10a1');
		$this->_create_node(1000);

  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('node_test');
		
  	$id = $this->object->create();
  	
  	$this->assertEqual($this->object->get_identifier(), '10ru3');
  }
  
  function _create_node($identifier)
  {
  	static $object_id = 1;
  	
  	$tree =& tree :: instance();
  	
		$values['identifier'] = $identifier;
		$values['object_id'] = ++$object_id;
		$tree->create_sub_node($this->parent_node_id, $values);

		$class_id = $this->object->get_class_id();
		$this->db->sql_insert('sys_site_object', 
			array('id' => $object_id, 'class_id' => $class_id, 'current_version' => 1));
  }
  	      	
}

?>