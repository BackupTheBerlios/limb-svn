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
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');
require_once(dirname(__FILE__) . '/../../../simple_authorizer.class.php');

Mock::generatePartial
(
  'simple_authorizer',
  'simple_authorizer_test_version',
  array('_get_controller')
); 

Mock::generate('site_object_controller');
  
class simple_authorizer_test extends LimbTestCase 
{  	
	var $auth = null;

	var $objects_to_filter = array();

	var $objects_to_assign_actions  = array();
	
	var $site_object_controller_actions = array();
	  
  function setUp()
  {
  	load_testing_db_dump(dirname(__FILE__) . '/../../sql/simple_authorizer.sql');
  	
  	$this->auth = new simple_authorizer_test_version($this);
  }
  
  function tearDown()
  {
  	user :: instance()->logout();
  	
  	$this->auth->tally();
  	
  	clear_testing_db_tables();
  }

 	function test_get_accessible_objects_ok()
 	{
 	  user :: instance()->import(array('id' => 200, 'groups' => array(100 => 'admins')));

  	$object_ids = $this->auth->get_accessible_object_ids(array(300, 300, 301, 302, 303));
  	
  	$this->assertEqual(sizeof($object_ids), 3);
 	}

 	function test_get_accessible_objects_ok2()
 	{
 	  user :: instance()->import(array('id' => 210));
		
  	$object_ids = $this->auth->get_accessible_object_ids(array(300, 300, 301, 302, 303));
  	
  	$this->assertEqual(sizeof($object_ids), 2);
  	$this->assertEqual($object_ids, array(302, 303));
 	}
 
 	function test_get_accessible_objects_using_class_restriction()
 	{
 	  user :: instance()->import(array('id' => 210));
    
  	$object_ids = $this->auth->get_accessible_object_ids(array(300, 300, 301, 302, 303), '', 11);
  	
  	$this->assertEqual(sizeof($object_ids), 1);
  	$this->assertEqual($object_ids, array(303));
 	}

 	function test_assign_actions_1()
 	{
  	$controller_actions = array(
			'display' => array(),
			'create' => array(),
			'edit' => array(),
			'publish' => array(),
			'delete' => array(),
		);
		
		$m = new Mocksite_object_controller($this);
		$m->setReturnValue('get_actions_definitions', $controller_actions);
		
		$this->auth->expectOnce('_get_controller');
		$this->auth->setReturnReference('_get_controller', $m, array('site_object_access_test'));

  	$objects_to_assign_actions = array(
  		1 => array(
  			'id' => 300,
  			'class_id' => 10,
  			'class_name' => 'site_object_access_test',
  		),
  	);

		user :: instance()->import(array('id' => 200, 'groups' => array(100 => 'admins')));

  	$this->auth->assign_actions_to_objects($objects_to_assign_actions);
  	
  	$m->tally();
  	
  	$obj = reset($objects_to_assign_actions);
  	$this->assertEqual(sizeof($obj['actions']), 4);
  	
  	$this->assertEqual($obj['actions'],
  		array(
  			'create' => array(),
  			'display' => array(),
  			'edit' => array(),
  			'delete' => array()
  		)
  	);	
 	}

 	function test_assign_actions_2()
 	{
  	$controller_actions = array(
			'display' => array(),
			'create' => array(),
			'edit' => array(),
			'publish' => array(),
			'delete' => array(),
		);
		
		$m = new Mocksite_object_controller($this);
		$m->setReturnValue('get_actions_definitions', $controller_actions);
		
		$this->auth->expectOnce('_get_controller');
		$this->auth->setReturnReference('_get_controller', $m, array('site_object_access_test'));

  	$objects_to_assign_actions = array(
  		1 => array(
  			'id' => 302,
  			'class_id' => 11,
  			'class_name' => 'site_object_access_test',
  		),
  	);

		user :: instance()->import(array('id' => 200, 'groups' => array(100 => 'admins')));

  	$this->auth->assign_actions_to_objects($objects_to_assign_actions);
  	
  	$m->tally();
  	
  	$obj = reset($objects_to_assign_actions);
  	$this->assertEqual(sizeof($obj['actions']), 1);
  	
  	$this->assertEqual($obj['actions'],
  		array(
  			'display' => array(),
  		)
  	);	
 	}
 	 
}

?>