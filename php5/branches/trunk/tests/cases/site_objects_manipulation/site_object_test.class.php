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
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/behaviours/site_object_behaviour.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('site_object_behaviour');

Mock :: generatePartial
(
  'site_object',
  'site_object_test_version',
  array('_can_add_node_to_parent')
); 


class controller_test{}

class site_object_test extends LimbTestCase 
{ 
	var $db;
	var $object;
  var $toolkit;
		 	
  function setUp()
  {
  	$this->db = db_factory :: instance();
    $this->toolkit = new MockLimbToolkit($this);
  	
  	$this->_clean_up();
  	
  	$this->object = new site_object_test_version($this);  	
  	$this->object->__construct();
  	
  	user :: instance()->set('id', 10);
  }
  
  function tearDown()
  { 
  	$this->_clean_up();

  	$user = user :: instance();  	
  	$user->logout();

  	$this->object->tally();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_site_object');
  	$this->db->sql_delete('sys_site_object_tree');
  	$this->db->sql_delete('sys_object_version');
  	$this->db->sql_delete('sys_class');
    $this->db->sql_delete('sys_behaviour');
  }

  function test_attributes()
  {
  	$this->object->set('attrib_name', 'attrib_value');
  	$this->assertEqual($this->object->get('attrib_name'), 'attrib_value');
  	
  	$attribs['attrib1'] = 'attrib1_value';
  	$attribs['attrib2'] = 'attrib2_value';
  	$this->object->merge($attribs);

  	$attribs['attrib_name'] = 'attrib_value';
  	
  	$this->assertEqual($this->object->export(), $attribs);
  	
  	$this->object->set('attrib3_name', 'attrib3_value');
    unset($attribs['attrib_name']);
  	$this->object->import($attribs);
  	$this->assertEqual($this->object->export(), $attribs);
  }
  
  function test_get_behaviour()
  {
    Limb :: registerToolkit($this->toolkit);
    
    $this->db->sql_insert('sys_behaviour', array('id' => $behaviour_id = 100,
                                                 'name' => 'test_behaviour'));

     
    $mock_behaviour = new Mocksite_object_behaviour($this);
    
    $this->toolkit->expectOnce('createBehaviour', array('test_behaviour'));
    $this->toolkit->setReturnValue('createBehaviour', $mock_behaviour);
    
    $this->object->set_behaviour_id($behaviour_id);
     
  	$this->assertTrue($mock_behaviour === $this->object->get_behaviour());

    Limb :: popToolkit();  	
  }

  function test_get_class_id()
  {
    // autogenerate class_id
		$id = $this->object->get_class_id();
		
		$this->db->sql_select('sys_class', '*', 'name="' . get_class($this->object) . '"');
		$arr = $this->db->fetch_row();
		
		$this->assertNotNull($id);
		
		$this->assertEqual($id, $arr['id']);

    // generate class_id only once
		$id = $this->object->get_class_id();
		$this->db->sql_select('sys_class', '*');
		$arr = $this->db->get_array();
		
		$this->assertEqual(sizeof($arr), 1);
	}
}

?>