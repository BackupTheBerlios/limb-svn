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
require_once(LIMB_DIR . 'core/actions/action.class.php');  
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');

Mock::generate('template');
Mock::generate('action');

Mock::generatePartial
(
  'site_object_controller',
  'site_object_controller_test_version1',
  array('get_actions_definitions')
); 

Mock::generatePartial
(
  'site_object_controller',
  'site_object_controller_test_version2',
  array(
  'get_actions_definitions', 
  'get_current_action_property', 
  '_create_template',
  '_create_action')
); 

class test_site_object_controller extends UnitTestCase 
{ 
	var $site_object_controller = null;
	 	
  function test_site_object_controller() 
  {
  	parent :: UnitTestCase();
  }
  
  function setUp()
  {
  	$_REQUEST['action'] = 'test_action';
  	
  	$this->site_object_controller =& new site_object_controller_test_version1($this);
 
  	$actions = array(
  		'display' => array(
  		),
			'test_action' => array(
				'permissions_required' => 'w',
				'action_path' => 'action',
				'transaction_required' => false
			),
			'publish' => array(
				'permissions_required' => 'w',
			)
		);
  	
  	$this->site_object_controller->setReturnValue('get_actions_definitions', $actions);
  	
  	$this->site_object_controller->site_object_controller();  	
  }
  
  function tearDown()
  {
		$this->site_object_controller->tally();
		unset($this->site_object_controller);
		unset($_REQUEST['action']);
  }
    
  function test_action_exists()
  {
  	$this->assertTrue($this->site_object_controller->action_exists('test_action'));
  	$this->assertFalse($this->site_object_controller->action_exists('test_no_such_action'));
  }
  
  function test_determine_action()
  { 
  	$this->assertNotIdentical($this->site_object_controller->determine_action(), false);
  	$this->assertEqual($this->site_object_controller->get_action(), 'test_action');
  }
  
  function test_default_determine_action()
  {
  	unset($_REQUEST['action']);
  	
  	$this->site_object_controller->site_object_controller();
  	
		$this->assertNotIdentical($this->site_object_controller->determine_action(), false);
  	$this->assertEqual($this->site_object_controller->get_action(), 'display');
  }
  
  function test_process()
  { 
  	$_REQUEST['action'] = 'test_action';
  	
  	$action =& new Mockaction($this);
  	$template =& new Mocktemplate($this);
  	
  	$site_object_controller =& new site_object_controller_test_version2($this);
 
   	$actions = array(
  		'display' => array(),
			'test_action' => array(
				'permissions_required' => 'w',
				'action_path' => 'action',
				'template_path' => 'test_template'
			),
			'publish' => array(
				'permissions_required' => 'w',
			)
		);
  	
  	$site_object_controller->setReturnValue('get_actions_definitions', $actions);
 	
  	$this->assertNotIdentical($site_object_controller->determine_action(), false);
  	$this->assertEqual($site_object_controller->determine_action(), 'test_action');
 
  	$site_object_controller->expectAtLeastOnce('get_current_action_property');
  	$site_object_controller->setReturnValue('get_current_action_property', 'action', array('action_path'));
  	$site_object_controller->expectOnce('_create_action', array('action'));
  	$site_object_controller->setReturnReference('_create_action', $action);
  	
  	$site_object_controller->setReturnValue('get_current_action_property', 'test_template', array('template_path'));
  	$site_object_controller->expectOnce('_create_template', array('test_template'));
  	$site_object_controller->setReturnReference('_create_template', $template);
  	 
   	$action->expectOnce('perform');
  	$action->setReturnValue('perform', true);
  	$action->expectOnce('set_view');
 	  	
  	$site_object_controller->site_object_controller();

  	$this->assertTrue($site_object_controller->process(), __LINE__);
  	
  	$site_object_controller->tally();
  	$action->tally();
  	$template->tally();
  } 
  
  function test_process_no_action()
  { 
  	$_REQUEST['action'] = 'test_action';
  	
  	$action =& new Mockaction($this);
  	$template =& new Mocktemplate($this);
  	
  	$site_object_controller =& new site_object_controller_test_version2($this);
 
  	$actions = array(
  		'display' => array(),
			'test_action' => array(
				'permissions_required' => 'w',
				'template_path' => 'test_template'
			),
			'publish' => array(
				'permissions_required' => 'w',
			)
		);
  	
  	$site_object_controller->setReturnValue('get_actions_definitions', $actions);
  	
  	$this->assertNotIdentical($site_object_controller->determine_action(), false);

  	$site_object_controller->expectAtLeastOnce('get_current_action_property');
  	$site_object_controller->setReturnValue('get_current_action_property', null, array('action_path'));
  	$site_object_controller->expectNever('_create_action');
  	
  	$site_object_controller->setReturnValue('get_current_action_property', 'test_template', array('template_path'));
  	$site_object_controller->expectNever('_create_template');
  	  	  	
  	$site_object_controller->site_object_controller();

  	$this->assertTrue($site_object_controller->process());
  	
  	$site_object_controller->tally();
  	$action->tally();
  	$template->tally();
  } 
  
  function test_get_action_object()
  {
  	$this->assertNotIdentical($this->site_object_controller->determine_action(), false);
  	$action =& $this->site_object_controller->get_action_object();
  	
  	$this->assertNotNull($action);
  	$this->assertIsA($action, 'action');
  }
  
  function test_process_no_template()
  { 
  	$_REQUEST['action'] = 'test_action';
  	
  	$action =& new Mockaction($this);
  	$template =& new Mocktemplate($this);
  	
  	$site_object_controller =& new site_object_controller_test_version2($this);
 
  	$actions = array(
			'test_action' => array(
				'permissions_required' => 'w',
				'action_path' => 'action',
				'template_path' => 'test_template'
			),
			'publish' => array(
				'permissions_required' => 'w',
			)
		);
  	
  	$site_object_controller->setReturnValue('get_actions_definitions', $actions);
  	
  	$this->assertNotIdentical($site_object_controller->determine_action(), false);

  	$site_object_controller->expectAtLeastOnce('get_current_action_property');
  	$site_object_controller->setReturnValue('get_current_action_property', 'action', array('action_path'));
  	$site_object_controller->expectOnce('_create_action', array('action'));
  	$site_object_controller->setReturnReference('_create_action', $action);
  	
  	$site_object_controller->setReturnValue('get_current_action_property', null, array('template_path'));
  	$site_object_controller->expectNever('_create_template');
  	 
   	$action->expectOnce('perform');
  	$action->setReturnValue('perform', true);
  	$action->expectNever('set_view');
 	  	
  	$site_object_controller->site_object_controller();

  	$this->assertTrue($site_object_controller->process());
  	
  	$site_object_controller->tally();
  	$action->tally();
  	$template->tally();
  }
  
}
?>