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

require_once(LIMB_DIR . '/tests/cases/test_limb_case.php');

require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . 'core/model/access_policy.class.php');

Mock::generatePartial
(
  'access_policy',
  'access_policy_test_version',
  array('_get_controller')
); 

Mock::generate('site_object_controller');
  
class test_access_policy extends test_limb_case 
{  	
	var $dump_file = 'access_policy_load.sql';

	var $ac = null;

	var $objects_to_filter = array();

	var $objects_to_assign_actions  = array();
	
	var $site_object_controller_actions = array();
	
  function test_access_policy() 
  {
  	parent :: test_limb_case();
  }
  
  function setUp()
  {
  	parent :: setUp();
		
  	$this->ac = new access_policy_test_version($this);

  	$this->objects_to_assign_actions = array(
  		1 => array(
  			'id' => 300,
  			'class_id' => 10,
  			'class_name' => 'site_object_access_test',
  		),
  		2 => array(
  			'id' => 302,
  			'class_id' => 10,
  			'class_name' => 'site_object_access_test',
  		),
  		3 => array(
  			'id' => 303,
  			'class_id' => 10,
  			'class_name' => 'site_object_access_test',
  		)
  	);

		$this->objects_to_filter = array(300, 300, 301, 302, 303);

		$this->site_object_controller_actions = array(
				'display' => array(
						'permissions_required' => 'r',
				),
				'create' => array(
						'permissions_required' => 'w',
				),
				'edit' => array(
						'permissions_required' => 'rw',
				),
				'publish' => array(
						'permissions_required' => 'w',
				),
				'delete' => array(
						'permissions_required' => 'w',
				),				
		);
  }
  
  function tearDown()
  {
  	parent :: tearDown();
  	$this->ac->tally();
  }
	
 	function test_get_accessible_objects()
 	{
		$this->_login_user(200, array(100 => 'admins'));
		
  	$object_ids = $this->ac->get_accessible_objects($this->objects_to_filter, 'r');
  	
  	$this->assertEqual(sizeof($object_ids), 3);
  	
  	$object_ids = $this->ac->get_accessible_objects($this->objects_to_filter, 'w');
  	
  	$this->assertEqual(sizeof($object_ids), 3);

		$this->_login_user(210, array(100 => 'admins'));
		
  	$object_ids = $this->ac->get_accessible_objects($this->objects_to_filter, 'rw');
  	
  	$this->assertEqual(sizeof($object_ids), 2);
 	}
 	
 	function test_assign_actions()
 	{
		$this->_login_user(200, array(100 => 'admins'));
		
		$m =& new Mocksite_object_controller($this);
		$m->setReturnValue('get_actions_definitions', $this->site_object_controller_actions);
		
		$this->ac->expectOnce('_get_controller');
		$this->ac->setReturnReference('_get_controller', $m, array('site_object_access_test'));

  	$this->ac->assign_actions_to_objects($this->objects_to_assign_actions);
  	
  	$m->tally();
  	
  	$obj = $this->objects_to_assign_actions[1];
  	$this->assertEqual(sizeof($obj['actions']), 4);
  	
  	$this->assertEqual($obj['actions'],
  		array(
  			'create' => array(
  				'permissions_required' => 'w'
  			),
  			'display' => array(
  				'permissions_required' => 'r'
  			),
  			'edit' => array(
  				'permissions_required' => 'rw'
  			),
  			'delete' => array(
  				'permissions_required' => 'w'
  			),
  		)
  	);	

  	$obj = $this->objects_to_assign_actions[2];
  	$this->assertEqual(sizeof($obj['actions']), 1);
  	
  	$this->assertEqual($obj['actions'],
  		array(
  			'display' => array(
  				'permissions_required' => 'r'
  			),
  		)
  	);	
  	
  	$obj = $this->objects_to_assign_actions[3];
  	$this->assertEqual(sizeof($obj['actions']), 2);
  	
  	$this->assertEqual($obj['actions'],
  		array(
  			'create' => array(
  				'permissions_required' => 'w'
  			),
  			'delete' => array(
  				'permissions_required' => 'w'
  			),
  		)
  	);

 	} 
 	
}

?>