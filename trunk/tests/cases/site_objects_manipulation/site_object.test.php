<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: site_object.test.php 81 2004-03-26 13:51:05Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/model/site_objects/site_object.class.php');

class site_object_test_version extends site_object
{
	function site_object_test_version()
	{
		parent :: site_object();
	}
	
	function _define_attributes_definition()
	{
		return array(
			'title' => '',
			'name' => array('type' => 'numeric'),
			'search' => array('search' => true),
		);		
	}
	
	function _define_class_properties()
	{
		return array(
			'ordr' => 1,
			'can_be_parent' => 1,
			'db_table_name' => 'site_object',
			'controller_class_name' => 'test_controller'
		);
	}
}


Mock::generatePartial
(
  'site_object_test_version',
  'mocked_site_object_test_version',
  array('_can_add_node_to_parent')
); 


class test_controller
{
}

class test_site_object extends UnitTestCase 
{ 
	var $db = null;
	var $object = null;
		 	
  function test_site_object() 
  {
  	$this->db =& db_factory :: instance();

  	parent :: UnitTestCase();
  }

  function setUp()
  {
  	$this->_clean_up();
  	
  	$this->object = new mocked_site_object_test_version($this);
  	
  	$this->object->site_object();
  	
  	$user =& user :: instance();
  	$user->_set_id(10);
  }
  
  function tearDown()
  { 
  	$this->_clean_up();

  	$user =& user :: instance();  	
  	$user->logout();

  	$this->object->tally();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
  	$this->db->sql_delete('sys_site_object_tree');
  	$this->db->sql_delete('sys_object_version');
  	$this->db->sql_delete('sys_class');
  }

  function test_attributes()
  {
  	$this->object->set_attribute('attrib_name', 'attrib_value');
  	$this->assertEqual($this->object->get_attribute('attrib_name'), 'attrib_value');
  	
  	$attribs['attrib1'] = 'attrib1_value';
  	$attribs['attrib2'] = 'attrib2_value';
  	$this->object->import_attributes($attribs);

  	$attribs['attrib_name'] = 'attrib_value';
  	
  	$this->assertEqual($this->object->export_attributes(), $attribs);
  	
  	$this->object->set_attribute('attrib3_name', 'attrib3_value');
  	unset($attribs['attrib_name']);
  	$this->object->import_attributes($attribs, false);
  	$this->assertEqual($this->object->export_attributes(), $attribs);
  	
  }
  
  function test_attributes_definition()
  {  	
  	$this->assertIdentical($this->object->get_attribute_definition('no_such_attribute'), false);
  	
  	$definition = $this->object->get_attribute_definition('id');
  	
  	$this->assertEqual($definition['type'], 'numeric');
  }
  
  function test_get_controller()
  {
  	$ctrl =& $this->object->get_controller();
  	
  	$this->assertNotNull($ctrl);
  	$this->assertIsA($ctrl, 'test_controller');
  }

  function test_get_class_id()
  {
		$id = $this->object->get_class_id();
		
		$this->db->sql_select('sys_class', '*', 'class_name="' . get_class($this->object) . '"');
		$arr = $this->db->fetch_row();
		
		$this->assertNotNull($id);
		
		$this->assertEqual($id, $arr['id']);

		$id = $this->object->get_class_id();
		$this->db->sql_select('sys_class', '*');
		$arr = $this->db->get_array();
		
		$this->assertEqual(sizeof($arr), 1);
	}
	
	function test_can_be_parent()
	{
		$this->assertTrue($this->object->can_be_parent());
	}
}

?>
