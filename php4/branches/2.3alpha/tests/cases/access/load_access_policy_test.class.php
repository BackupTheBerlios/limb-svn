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
require_once(LIMB_DIR . '/tests/cases/db_test.class.php');
require_once(LIMB_DIR . '/core/model/access_policy.class.php');

class load_access_policy_test extends db_test 
{  	
	var $dump_file = 'access_policy_load.sql';

	var $ac = null;
	  
  function setUp()
  {
  	parent :: setUp();
  	
  	$this->ac =& access_policy :: instance();
  }
  
  function tearDown()
  {
  }

	function test_get_user_object_access_by_ids()
	{
		$ids = array(300, 301, 302, 303);
		$user_objects_access = $this->ac->get_user_object_access_by_ids($ids);
		
		$this->assertEqual(sizeof($user_objects_access), 3);
		
		$this->assertEqual($user_objects_access[300], 
			array(
				200 => array('r' => 1, 'w' => 0),
				210 => array('r' => 1, 'w' => 0),
			)	
		);

		$this->assertEqual($user_objects_access[303], 
			array(
				200 => array('r' => 0, 'w' => 1),
			)	
		);
	} 
	
	function test_get_group_user_object_access_by_ids()
	{
		$ids = array(300, 301, 302, 303);
		$group_objects_access = $this->ac->get_group_object_access_by_ids($ids);
		
		$this->assertEqual(sizeof($group_objects_access), 4);

		$this->assertEqual($group_objects_access[303], 
			array(
				100 => array('r' => 0, 'w' => 1),
				110 => array('r' => 1, 'w' => 0),
			)	
		);

		$this->assertEqual($group_objects_access[301], 
			array(
				100 => array('r' => 1, 'w' => 1),
				110 => array('r' => 1, 'w' => 0),
			)	
		);

		$this->assertEqual($group_objects_access[300], 
			array(
				100 => array('r' => 1, 'w' => 1),
				110 => array('r' => 1, 'w' => 0),
			)	
		);
	}

	function test_load_user_action_access()
	{
		$user_actions_access = $this->ac->get_user_action_access_by_controller(10);
		
		$this->assertEqual($user_actions_access, 
			array(
				200 => array('create' => 1, 'edit' => 1, 'delete' => 1),
			)	
		);

		$user_actions_access = $this->ac->get_user_action_access_by_controller(12);

		$this->assertEqual($user_actions_access, 
			array(
				210 => array('delete' => 1),
			)	
		);
	}

	function test_load_group_action_access()
	{
		$group_actions_access = $this->ac->get_group_action_access_by_controller(10);
		
		$this->assertEqual($group_actions_access, 
			array(
				100 => array('display' => 1, 'create' => 1, 'edit' => 1),
				110 => array('display' => 1),
			)
		);

		$group_actions_access = $this->ac->get_group_action_access_by_controller(12);

		$this->assertEqual($group_actions_access, 
			array(
				100 => array('edit' => 1, 'create' => 1),
				110 => array('create' => 1),
			)
		);
	}
}

?>