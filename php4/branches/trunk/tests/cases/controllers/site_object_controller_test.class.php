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
require_once(LIMB_DIR . '/core/actions/action.class.php');  
require_once(LIMB_DIR . '/core/request/response.class.php');  
require_once(LIMB_DIR . '/core/request/request.class.php');  
require_once(LIMB_DIR . '/core/controllers/site_object_controller.class.php');

Mock::generate('template');
Mock::generate('action');
Mock::generate('response');
Mock::generate('request');

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

class site_object_controller_test extends UnitTestCase 
{ 
	var $site_object_controller = null;
	var $request = null;
	var $response = null;
	
	var $actions_definition_test = array(
  		'display' => array(
  		),
			'action_test' => array(
				'permissions_required' => 'w',
				'action_path' => 'action',
			),
			'publish' => array(
				'permissions_required' => 'w',
				'transaction' => false
			)
		); 
	 	  
  function setUp()
  {
  	$this->request = new Mockrequest($this);
  	$this->response = new Mockresponse($this);
  	
  	$this->site_object_controller =& new site_object_controller_test_version1($this);
  	$this->site_object_controller->setReturnValue('get_actions_definitions', $this->actions_definition_test);
  	$this->site_object_controller->site_object_controller();
  	
  	debug_mock :: init($this);
  }
  
  function tearDown()
  {
		$this->site_object_controller->tally();
		unset($this->site_object_controller);
		
		debug_mock :: tally();
		
		$this->request->tally();
		$this->response->tally();
  }
      
  function test_action_exists()
  {
  	$this->assertTrue($this->site_object_controller->action_exists('action_test'));
  	$this->assertFalse($this->site_object_controller->action_exists('no_such_action_test'));
  }
  
  
  function test_determine_action()
  { 
  	$this->request->setReturnValue('get_attribute', 'action_test', array('action'));
  
  	$this->assertNotIdentical($this->site_object_controller->determine_action($this->request), false);
  	$this->assertEqual($this->site_object_controller->get_action(), 'action_test');
  }
  
  function test_default_determine_action()
  {
  	$this->assertNotIdentical($this->site_object_controller->determine_action($this->request), false);
  	$this->assertEqual($this->site_object_controller->get_action(), 'display');
  }
  
  function test_get_action_object()
  {
  	$this->request->setReturnValue('get_attribute', 'action_test', array('action'));

  	$this->assertNotIdentical($this->site_object_controller->determine_action($this->request), false);
  	$action =& $this->site_object_controller->get_action_object();
  	
  	$this->assertNotNull($action);
  	$this->assertIsA($action, 'action');
  }
  
  function test_get_empty_action_object()
  {
  	$this->request->setReturnValue('get_attribute', 'no_such_action', array('action'));

  	debug_mock :: expect_write_warning('action not found', 
  		array (
					  'class' => 'site_object_controller_test_version1',
					  'action' => 'no_such_action',
					  'default_action' => 'display',
					)
		);
  	
  	$this->assertIdentical($this->site_object_controller->determine_action($this->request), false);
  	$action =& $this->site_object_controller->get_action_object();
  	
  	$this->assertNotNull($action);
  	$this->assertIsA($action, 'empty_action');
  }

  function test_display_view()
  { 
  	$template =& new Mocktemplate($this);
  	
  	$site_object_controller =& new site_object_controller_test_version2($this);
  	$site_object_controller->setReturnValue('get_actions_definitions', $this->actions_definition_test);
  	
  	$this->request->setReturnValue('get_attribute', 'action_test', array('action'));
  	
  	$this->assertEqual($site_object_controller->determine_action($this->request), 'action_test');

  	$site_object_controller->expectOnce('_create_template');
  	$site_object_controller->setReturnReference('_create_template', $template);
  	
  	$template->expectOnce('display');
  	$site_object_controller->display_view();
  	
  	$site_object_controller->tally();
  	$template->tally();
  }

  function test_display_empty_view()
  { 
  	$this->request->setReturnValue('get_attribute', 'no_such_action', array('action'));
  	
  	debug_mock :: expect_write_warning('action not found', 
  		array (
					  'class' => 'site_object_controller_test_version1',
					  'action' => 'no_such_action',
					  'default_action' => 'display',
					)
		);

  	$this->assertIdentical($this->site_object_controller->determine_action($this->request), false);

		debug_mock :: expect_write_error('template is null');
		
  	$this->site_object_controller->display_view();  	
  }  
  
  function test_transaction_required()
  {
  	$this->request->setReturnValue('get_attribute', 'action_test', array('action'));
  
  	$this->site_object_controller->determine_action($this->request);
  	$this->assertTrue($this->site_object_controller->is_transaction_required());
  }
  
  function test_transaction_not_required()
  {
  	$this->request->setReturnValue('get_attribute', 'publish', array('action'));
  	
  	$this->site_object_controller->determine_action($this->request);
  	$this->assertFalse($this->site_object_controller->is_transaction_required());
  }
    
  function test_process()
  { 
  	$action =& new Mockaction($this);
  	$template =& new Mocktemplate($this);
  	
  	$site_object_controller =& new site_object_controller_test_version2($this);
   	
  	$site_object_controller->setReturnValue('get_actions_definitions', $this->actions_definition_test);

  	$this->request->setReturnValue('get_attribute', 'action_test', array('action'));
 	
  	$this->assertNotIdentical($site_object_controller->determine_action($this->request), false);
  	$this->assertEqual($site_object_controller->determine_action($this->request), 'action_test');
 
  	$site_object_controller->expectOnce('_create_action', array('action'));
  	$site_object_controller->setReturnReference('_create_action', $action);
  	
  	$site_object_controller->expectOnce('_create_template');
  	$site_object_controller->setReturnReference('_create_template', $template);
  	
   	$action->expectOnce('perform', array(new IsAExpectation('Mockrequest'), new IsAExpectation('Mockresponse')));
  	$action->expectOnce('set_view');
 	  	
  	$site_object_controller->site_object_controller();

  	$site_object_controller->process($this->request, $this->response);
  	
  	$site_object_controller->tally();
  	$action->tally();
  } 
}
?>
