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
require_once(LIMB_DIR . 'class/core/actions/action.class.php');  
require_once(LIMB_DIR . 'class/core/request/http_response.class.php');  
require_once(LIMB_DIR . 'class/core/request/request.class.php');  
require_once(LIMB_DIR . 'class/core/controllers/site_object_controller.class.php');

Mock::generate('template');
Mock::generate('action');
Mock::generate('http_response');
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

class site_object_controller_test extends LimbTestCase 
{ 
	var $site_object_controller = null;
	var $request = null;
	var $response = null;
	
	var $actions_definition_test = array(
  		'display' => array(
  		),
			'action_test' => array(
				'action_path' => 'action',
			),
			'publish' => array(
				'transaction' => false
			)
		); 
	 	  
  function setUp()
  {
  	$this->request = new Mockrequest($this);
  	$this->response = new Mockhttp_response($this);
  	
  	$this->site_object_controller = new site_object_controller_test_version1($this);
  	$this->site_object_controller->setReturnValue('get_actions_definitions', $this->actions_definition_test);
  	$this->site_object_controller->__construct();
  }
  
  function tearDown()
  {
		$this->site_object_controller->tally();
	  unset($this->site_object_controller);
		
		$this->request->tally();
		$this->response->tally();
  }
      
  function test_action_exists()
  {
  	$this->assertTrue($this->site_object_controller->action_exists('action_test'));
  	$this->assertFalse($this->site_object_controller->action_exists('no_such_action_test'));
  }
  
  function test_get_action()
  { 
  	$this->request->setReturnValue('get', 'action_test', array('action'));
  
  	$this->assertEqual($this->site_object_controller->get_action($this->request), 'action_test');
  }
  
  function test_default_get_action()
  {
  	$this->assertEqual($this->site_object_controller->get_action($this->request), 'display');
  }
  
  function test_get_action_object()
  {
  	$this->request->setReturnValue('get', 'action_test', array('action'));

  	$action =& $this->site_object_controller->get_action_object($this->request);
  	
  	$this->assertNotNull($action);
  	$this->assertIsA($action, 'action');
  }
  
  function test_get_no_such_action()
  {
  	$this->request->setReturnValue('get', 'no_such_action', array('action'));

  	try
  	{
  	  $action =& $this->site_object_controller->get_action($this->request);
  	  $this->assertTrue(false);
  	} 
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'action not found');
  	}
  }
  
  function test_get_empty_action_object()
  {
  	$this->request->setReturnValue('get', 'no_such_action', array('action'));

  	$action = $this->site_object_controller->get_action_object($this->request);
  	
  	$this->assertNotNull($action);
  	$this->assertIsA($action, 'empty_action');
  }

  function test_display_view()
  { 
  	$template = new Mocktemplate($this);
  	
  	$site_object_controller = new site_object_controller_test_version2($this);
  	$site_object_controller->setReturnValue('get_actions_definitions', $this->actions_definition_test);
  	
  	$this->request->setReturnValue('get', 'action_test', array('action'));
  	
  	$this->assertEqual($site_object_controller->get_action($this->request), 'action_test');

  	$site_object_controller->expectOnce('_create_template');
  	$site_object_controller->setReturnReference('_create_template', $template);
  	
  	$template->expectOnce('display');
  	$site_object_controller->display_view($this->request);
  	
  	$site_object_controller->tally();
  	$template->tally();
  }

  function test_display_empty_view()
  { 
  	$this->request->setReturnValue('get', 'no_such_action', array('action'));
  	
  	try
  	{
  	  $this->site_object_controller->display_view($this->request);
  	  $this->assertTrue(false);
  	}
  	catch(LimbException $e)
  	{
  	  $this->assertEqual($e->getMessage(), 'template is empty');
  	}
  }  
  
  function test_transaction_required()
  {
  	$this->request->setReturnValue('get', 'action_test', array('action'));
  
  	$this->assertTrue($this->site_object_controller->is_transaction_required($this->request));
  }
  
  function test_transaction_not_required()
  {
  	$this->request->setReturnValue('get', 'publish', array('action'));
  	
  	$this->assertFalse($this->site_object_controller->is_transaction_required($this->request));
  }
    
  function test_process()
  { 
  	$action = new Mockaction($this);
  	$template = new Mocktemplate($this);
  	
  	$site_object_controller = new site_object_controller_test_version2($this);
   	
  	$site_object_controller->setReturnValue('get_actions_definitions', $this->actions_definition_test);

  	$this->request->setReturnValue('get', 'action_test', array('action'));
 	
  	$this->assertNotIdentical($site_object_controller->get_action($this->request), false);
  	$this->assertEqual($site_object_controller->get_action($this->request), 'action_test');
 
  	$site_object_controller->expectOnce('_create_action', array('action'));
  	$site_object_controller->setReturnReference('_create_action', $action);
  	
  	$site_object_controller->expectOnce('_create_template');
  	$site_object_controller->setReturnReference('_create_template', $template);
  	
   	$action->expectOnce('perform', array(new IsAExpectation('Mockrequest'), new IsAExpectation('Mockhttp_response')));
  	$action->expectOnce('set_view');
 	  	
  	$site_object_controller->__construct();

  	$site_object_controller->process($this->request, $this->response);
  	
  	$site_object_controller->tally();
  	$action->tally();
  } 
}
?>
