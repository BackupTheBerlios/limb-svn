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

require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/core/model/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/core/model/site_object_factory.class.php');
require_once(LIMB_DIR . '/tests/cases/fetching/site_object_fetch_accessible_test_init.php');

class site_object_fetch_accessible_test extends UnitTestCase 
{ 
	var $init_class_name = 'site_object_fetch_accessible_test_init';
	var $test_init = null;
	
	var $class_id = null;
	var $object = null;
	
  function site_object_fetch_accessible_test() 
  {
  	parent :: UnitTestCase();
  }

  function setUp()
  {
  	debug_mock :: init($this);

  	$this->test_init = new $this->init_class_name();
		
  	$this->_init_object();
  	$this->class_id = $this->object->get_class_id();

  	$this->test_init->init($this->object);
  }

  function _init_object()
  {
  	$this->object = new site_object_fetch_test_version();
  }
  
  function tearDown()
  { 
  	$this->test_init->_clean_up();
  	
  	$user =& user :: instance();
  	$user->logout();
  
		debug_mock :: tally();
  }
  
  function test_accesssible_no_params()
  {
  	$this->_login_user(200, array(100 => 'admins', 110 => 'users'));
  
  	$result = $this->object->fetch_accessible();
  	
  	for($i = 1; $i <=5; $i++)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  		$this->assertEqual($result[$i]['class_id'], $this->class_id);
  		$this->assertEqual($result[$i]['class_name'], get_class($this->object));
  	}

  	for($i = 6; $i <=7; $i++)
  		$this->assertFalse(isset($result[$i]));
  	
  	for($i = 8; $i <=10; $i++)
  	{
  		$this->assertEqual($result[$i]['identifier'], 'object_' . $i);
  		$this->assertEqual($result[$i]['title'], 'object_' . $i . '_title');
  		$this->assertEqual($result[$i]['class_id'], $this->class_id);
  		$this->assertEqual($result[$i]['class_name'], get_class($this->object));
  	}
  }

  function test_accesssible_count_no_params()
  {
  	$this->_login_user(200, array(100 => 'admins', 110 => 'users'));
  
  	$result = $this->object->fetch_accessible_count();
 		$this->assertEqual($result, 8);
  }
  
  function test_accessible_by_ids_no_ids()
  {
  	$this->_login_user(200, array(100 => 'admins', 110 => 'users'));
  	
		debug_mock :: expect_write_error('ids array is empty');
  	$result = $this->object->fetch_accessible_by_ids(array());
  	$this->assertEqual($result, array());
  }
  
  function test_accesssible_by_ids_no_params()
  {
  	$this->_login_user(200, array(100 => 'admins', 110 => 'users'));
  	
  	for($i = 1; $i <= 20; $i++)
  		$ids_array[] = $i;
  	
  	$result = $this->object->fetch_accessible_by_ids($ids_array);
  	
  	$ids = array(1, 2, 3, 4, 5, 8, 9, 10);
  	$this->assertEqual(array_keys($result) , $ids);
  }

  function test_accesssible_by_ids_count_no_params()
  {
  	$this->_login_user(200, array(100 => 'admins', 110 => 'users'));
  	
  	for($i = 1; $i <= 20; $i++)
  		$ids_array[] = $i;
  		
  	$result = $this->object->fetch_accessible_by_ids_count($ids_array);
  	
  	$this->assertEqual($result, 8);
  }

  function _login_user($id, $groups)
  {
  	$user =& user :: instance();
  	
  	$user->_set_id($id);
  	$user->_set_groups($groups);  	
  }
}

?>