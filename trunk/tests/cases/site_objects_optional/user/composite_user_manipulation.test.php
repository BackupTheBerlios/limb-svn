<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: user_manipulation.test.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/model/site_objects/composite_user_object.class.php');

Mock :: generatePartial(
'composite_user_object',
'composite_user_object_test_version',
array('send_activate_password_email')
);

Mock :: generate('user_object');

class test_composite_user_manipulation extends test_user_manipulation 
{ 
	var $node_obj1 = null;
	var $node_obj2 = null;
	 	
  function test_composite_user_manipulation() 
  {
  	parent :: test_user_manipulation();
  }

  function & _create_site_object()
  {
		$obj =& new composite_user_object_test_version($this);
		$obj->composite_user_object();
		
		$this->node_obj1 =& new Mockuser_object($this);
		$this->node_obj2 =& new Mockuser_object($this);
		
		$obj->_register_node_object($this->node_obj1);
		$obj->_register_node_object($this->node_obj2);
		
  	return $obj;
  }
  
  function tearDown()
  {
  	parent :: tearDown();
  	
  	$this->node_obj1->tally();
  	$this->node_obj2->tally();
  }
  
  function test_create()
  {
  	$this->_set_nodes_objects_return_values('create', true, true);
  	$this->_expect_nodes_objects_call('create');
  	$this->_expect_nodes_objects_call('import_attributes');
  	
  	parent :: test_create();
  }

  function test_composite_failed_create()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_site_object');

  	$this->_set_nodes_objects_return_values('create', true, false);
  	$this->_expect_nodes_objects_call('create');
  	
  	$this->assertIdentical($this->object->create(), false);
  }

  function test_versioned_update()
  {
  	$this->_set_nodes_objects_return_values('update', true, true);
  	$this->_expect_nodes_objects_call('update');
  	$this->_expect_nodes_objects_call('import_attributes');
  	
  	parent :: test_versioned_update();
  }

  function test_unversioned_update()
  {
  	$this->_set_nodes_objects_return_values('update', true, true);
  	$this->_expect_nodes_objects_call('update');
  	$this->_expect_nodes_objects_call('import_attributes');
  	
  	parent :: test_unversioned_update();
  }

  function test_delete()
  {
  	$this->_set_nodes_objects_return_values('delete', true, true);
  	$this->_expect_nodes_objects_call('delete');
  	
  	parent :: test_delete();
  }

  function test_composite_failed_delete()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_site_object');

  	$this->_set_nodes_objects_return_values('delete', true, false);
  	$this->_expect_nodes_objects_call('delete');
  	
  	$this->object->create();
  	$this->assertIdentical($this->object->delete(), false);
  }
  
  function test_change_password()
  {
  	$this->_set_nodes_objects_return_values('change_password', true, true);
  	$this->_expect_nodes_objects_call('change_password');
  	
  	parent :: test_change_password();
  }
  
  function test_composite_failed_change_password()
  {
  	$this->object->set_parent_node_id($this->parent_node_id);
  	$this->object->set_identifier('test_site_object');

  	$this->_set_nodes_objects_return_values('change_password', true, false);
  	$this->_expect_nodes_objects_call('change_password');
  	
  	$this->object->create();
  	$this->assertIdentical($this->object->change_password(), false);
  }
  
  function test_change_own_password()
  {
		$this->_set_nodes_objects_return_values('change_own_password', true, true);
		$this->_expect_nodes_objects_call('change_own_password');
		
  	parent :: test_change_own_password();
	}
		
	function test_activate_password()
	{
		$this->_set_nodes_objects_return_values('activate_password', true, true);
		$this->_expect_nodes_objects_call('activate_password');
		
		parent :: test_activate_password();
	}
	
	function test_generate_password()
	{
		$this->_set_nodes_objects_return_values('generate_password', true, true);
		$this->_expect_nodes_objects_call('generate_password');
		
		parent :: test_generate_password();
	}
	
	function _set_nodes_objects_return_values($function, $v1, $v2)
	{
  	$this->node_obj1->setReturnValue($function, $v1);
  	$this->node_obj2->setReturnValue($function, $v2);
	}
	
	function _expect_nodes_objects_call($function)
	{
  	$this->node_obj1->expectAtLeastOnce($function);
  	$this->node_obj2->expectAtLeastOnce($function);
	}
}

?>