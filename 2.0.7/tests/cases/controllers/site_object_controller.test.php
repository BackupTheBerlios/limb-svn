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
require_once(LIMB_DIR . 'core/model/response/response.class.php');  
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');

Mock::generate('template');
Mock::generate('action');
Mock::generate('response');

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
  '_create_template',
  '_create_action')
); 

class test_site_object_controller extends UnitTestCase 
{ 
	var $site_object_controller = null;
	
	var $test_actions_definition = array(
  		'display' => array(
  		),
			'test_action' => array(
				'permissions_required' => 'w',
				'action_path' => 'action',
			),
			'publish' => array(
				'permissions_required' => 'w',
				'transaction' => false
			)
		); 
	 	
  function test_site_object_controller() 
  {
  	parent :: UnitTestCase();
  }
  
  function setUp()
  {
  	$_REQUEST['action'] = 'test_action';
  	
  	$this->site_object_controller =& new site_object_controller_test_version1($this);
   	
  	$this->site_object_controller->setReturnValue('get_actions_definitions', $this->test_actions_definition);
  	
  	$this->site_object_controller->site_object_controller();
  	
  	debug_mock :: init($this);
  }
  
  function tearDown()
  {
		$this->site_object_controller->tally();
		unset($this->site_object_controller);
		unset($_REQUEST['action']);
		
		debug_mock :: tally();
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
     
  function test_get_action_object()
  {
  	$this->assertNotIdentical($this->site_object_controller->determine_action(), false);
  	$action =& $this->site_object_controller->get_action_object();
  	
  	$this->assertNotNull($action);
  	$this->assertIsA($action, 'action');
  }

  function test_get_empty_action_object()
  {
  	$_REQUEST['action'] = 'no_such_action';
  	
  	debug_mock :: expect_write_warning('action not found', 
  		array (
					  'class' => 'site_object_controller_test_version1',
					  'action' => 'no_such_action',
					  'default_action' => 'display',
					)
		);
  	
  	$this->assertIdentical($this->site_object_controller->determine_action(), false);
  	$action =& $this->site_object_controller->get_action_object();
  	
  	$this->assertNotNull($action);
  	$this->assertIsA($action, 'empty_action');
  }
  
  function test_display_view()
  { 
  	$template =& new Mocktemplate($this);
  	
  	$site_object_controller =& new site_object_controller_test_version2($this);
  	$site_object_controller->setReturnValue('get_actions_definitions', $this->test_actions_definition);
  	$this->assertEqual($site_object_controller->determine_action(), 'test_action');

  	$site_object_controller->expectOnce('_create_template');
  	$site_object_controller->setReturnReference('_create_template', $template);
  	
  	$template->expectOnce('display');
  	$site_object_controller->display_view();
  	
  	$site_object_controller->tally();
  	$template->tally();
  }

  function test_display_empty_view()
  { 
  	$_REQUEST['action'] = 'no_such_action';
  	
  	debug_mock :: expect_write_warning('action not found', 
  		array (
					  'class' => 'site_object_controller_test_version1',
					  'action' => 'no_such_action',
					  'default_action' => 'display',
					)
		);

  	$this->assertIdentical($this->site_object_controller->determine_action(), false);

		debug_mock :: expect_write_error('template is null');
		
  	$this->site_object_controller->display_view();  	
  }  
  
  function test_transaction_required()
  {
  	$this->site_object_controller->determine_action();
  	$this->assertTrue($this->site_object_controller->is_transaction_required());
  }

  function test_transaction_not_required()
  {
  	$_REQUEST['action'] = 'publish';
  	
  	$this->site_object_controller->determine_action();
  	$this->assertFalse($this->site_object_controller->is_transaction_required());
  }
    
  function test_process()
  { 
  	$action =& new Mockaction($this);
  	$response =& new Mockresponse($this);
  	$template =& new Mocktemplate($this);
  	
  	$site_object_controller =& new site_object_controller_test_version2($this);
   	
  	$site_object_controller->setReturnValue('get_actions_definitions', $this->test_actions_definition);
 	
  	$this->assertNotIdentical($site_object_controller->determine_action(), false);
  	$this->assertEqual($site_object_controller->determine_action(), 'test_action');
 
  	$site_object_controller->expectOnce('_create_action', array('action'));
  	$site_object_controller->setReturnReference('_create_action', $action);
  	
  	$site_object_controller->expectOnce('_create_template');
  	$site_object_controller->setReturnReference('_create_template', $template);
  	
  	$response->setReturnValue('get_status', RESPONSE_STATUS_SUCCESS);
  	 
   	$action->expectOnce('perform');
  	$action->setReturnReference('perform', $response);
  	$action->expectOnce('set_view');
 	  	
  	$site_object_controller->site_object_controller();

  	$this->assertIsA($site_object_controller->process(), 'mockresponse');
  	
  	$site_object_controller->tally();
  	$action->tally();
  } 

}
?>