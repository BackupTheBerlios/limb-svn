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
require_once(dirname(__FILE__) . '/../../../commands/apply_action_access_template_command.class.php');
require_once(dirname(__FILE__) . '/../../../access_policy.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/datasources/requested_object_datasource.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('request');
Mock :: generate('requested_object_datasource');
Mock :: generate('site_object');
Mock :: generate('site_object_controller');
Mock :: generate('access_policy');

Mock :: generatePartial(
                      'apply_action_access_template_command',
                      'apply_action_access_template_command_test_version',
                      array('_get_access_policy'));

class access_policy_for_apply_action_access_template_command extends access_policy
{
  public function apply_access_templates($object, $action)
  {
    throw new LimbException('catch me!');
  }
}

class apply_action_access_template_command_test extends LimbTestCase 
{
	var $command;
  var $request;
  var $toolkit;
  var $datasource;
  var $site_object;
  var $controller;
  var $access_policy;
		  	
  function setUp()
  {
    $this->request = new Mockrequest($this);
    $this->datasource = new Mockrequested_object_datasource($this);
    $this->site_object = new Mocksite_object($this);
    $this->controller = new Mocksite_object_controller($this); 
    $this->access_policy = new Mockaccess_policy($this);
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getDatasource', $this->datasource, array('requested_object_datasource'));
    $this->toolkit->setReturnValue('getRequest', $this->request);
    $this->toolkit->setReturnValue('createSiteObject', $this->site_object);
     
    $this->site_object->setReturnValue('get_controller', $this->controller);
    
    Limb :: registerToolkit($this->toolkit);
    
  	$this->command = new apply_action_access_template_command_test_version($this);
  }
  
  function tearDown()
  { 
    Limb :: popToolkit();
    
    $this->request->tally();
    $this->datasource->tally();
  	$this->toolkit->tally();
    $this->site_object->tally();
    $this->controller->tally();
    $this->access_policy->tally();
    $this->command->tally();
  }
          
  function test_perform_ok()
  {	
    $object_data = array('class_name' => 'site_object');
    
    $this->controller->expectOnce('get_action', array(new IsAExpectation('Mockrequest')));
    $this->controller->setReturnValue('get_action', $action = 'some_action');
    
  	$this->datasource->expectOnce('set_request', array(new IsAExpectation('Mockrequest')));
  	$this->datasource->expectOnce('fetch', array(new IsAExpectation('Mockrequest')));
  	$this->datasource->setReturnValue('fetch', $object_data);
    
    $this->access_policy->expectOnce('apply_access_templates', 
                                     array(new IsAExpectation('Mocksite_object'), $action));
    
    $this->command->setReturnValue('_get_access_policy', $this->access_policy);

    $this->assertEqual(Limb :: STATUS_OK, $this->command->perform());
  }

  function test_perform_failure()
  {	
    $object_data = array('class_name' => 'site_object');
    
  	$this->fetcher->setReturnValue('fetch_requested_object', $object_data);
    
    $this->command->setReturnValue('_get_access_policy', 
                                   new access_policy_for_apply_action_access_template_command());

    $this->assertEqual(Limb :: STATUS_ERROR, $this->command->perform());
  }
}

?>