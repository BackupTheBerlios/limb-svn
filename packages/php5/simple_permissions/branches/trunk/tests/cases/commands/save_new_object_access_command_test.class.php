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
require_once(dirname(__FILE__) . '/../../../commands/save_new_object_access_command.class.php');
require_once(dirname(__FILE__) . '/../../../access_policy.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/core/controllers/site_object_controller.class.php');
require_once(LIMB_DIR . '/class/core/dataspace.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('request');
Mock :: generate('fetcher');
Mock :: generate('site_object');
Mock :: generate('site_object_controller');
Mock :: generate('access_policy');
Mock :: generate('dataspace');

Mock :: generatePartial(
                      'save_new_object_access_command',
                      'save_new_object_access_command_test_version',
                      array('_get_access_policy'));

class access_policy_for_save_new_object_access_command extends access_policy
{
  public function save_new_object_access($object, $parent_object, $action)
  {
    throw new LimbException('catch me!');
  }
}

class save_new_object_access_command_test extends LimbTestCase 
{
	var $command;
  var $request;
	var $dataspace;
  var $toolkit;
  var $fetcher;
  var $site_object;
  var $parent_site_object;
  var $controller;
  var $access_policy;
		  	
  function setUp()
  {
    $this->request = new Mockrequest($this);
    $this->dataspace = new Mockdataspace($this);
    $this->fetcher = new Mockfetcher($this);
    $this->site_object = new Mocksite_object($this);
    $this->parent_site_object = new Mocksite_object($this);
    $this->controller = new Mocksite_object_controller($this); 
    $this->access_policy = new Mockaccess_policy($this);
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getFetcher', $this->fetcher);
    $this->toolkit->setReturnValue('getRequest', $this->request);
    $this->toolkit->setReturnValue('createSiteObject', $this->parent_site_object, array('site_object'));
    $this->toolkit->setReturnValue('getDataspace', $this->dataspace);
     
    Limb :: registerToolkit($this->toolkit);
    
  	$this->command = new save_new_object_access_command_test_version($this);
  }
  
  function tearDown()
  { 
    Limb :: popToolkit();
    
    $this->request->tally();
    $this->dataspace->tally();
    $this->fetcher->tally();
  	$this->toolkit->tally();
    $this->site_object->tally();
    $this->parent_site_object->tally();
    $this->controller->tally();
    $this->access_policy->tally();
    $this->command->tally();
  }
          
  function test_perform_ok()
  {	
    $this->access_policy->expectOnce('save_new_object_access',
                                     array(new IsAExpectation('Mocksite_object'),
                                           new IsAExpectation('Mocksite_object'),
                                           'some_action'));
    
    $this->dataspace->setReturnValue('get', $this->site_object, array('created_site_object'));

    $parent_object_data = array('class_name' => 'site_object');
  	$this->fetcher->expectOnce('fetch_one_by_node_id', array($parent_node_id = 100));
  	$this->fetcher->setReturnValue('fetch_one_by_node_id', $parent_object_data);
    
    $this->site_object->setReturnValue('get_parent_node_id', $parent_node_id);

    $this->controller->setReturnValue('get_action', 'some_action', array(new IsAExpectation('Mockrequest')));
    $this->controller->expectOnce('get_action', array(new IsAExpectation('Mockrequest')));
    $this->parent_site_object->setReturnValue('get_controller', $this->controller);
    
    $this->command->setReturnValue('_get_access_policy', $this->access_policy);

    $this->assertEqual(Limb :: STATUS_OK, $this->command->perform());
  }

  function test_perform_failure_access_policy_failed()
  {	
    $this->dataspace->setReturnValue('get', $this->site_object, array('created_site_object'));
    
    $this->command->setReturnValue('_get_access_policy', 
                                   new access_policy_for_save_new_object_access_command());

    $parent_object_data = array('class_name' => 'site_object');
  	$this->fetcher->expectOnce('fetch_one_by_node_id', array($parent_node_id = 100));
  	$this->fetcher->setReturnValue('fetch_one_by_node_id', $parent_object_data);

    $this->controller->setReturnValue('get_action', 'some_action', array(new IsAExpectation('Mockrequest')));
    $this->controller->expectOnce('get_action', array(new IsAExpectation('Mockrequest')));
    $this->parent_site_object->setReturnValue('get_controller', $this->controller);
    
    $this->site_object->setReturnValue('get_parent_node_id', $parent_node_id);
    
    $this->assertEqual(Limb :: STATUS_ERROR, $this->command->perform());
  }

  function test_perform_failure_no_created_object_data()
  {	
    $this->command->setReturnValue('_get_access_policy', 
                                   new access_policy_for_save_new_object_access_command());

    $this->dataspace->setReturnValue('get', null, array('created_site_object'));
    
    try
    {
      $this->command->perform();
      $this->assertTrue(false, 'Exception must be thrown');
    }
    catch(LimbException $e)
    {
    }
  }
}

?>