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
require_once(LIMB_DIR . '/class/core/commands/form_create_site_object_command.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/fetcher.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/validators/validator.class.php');

Mock::generate('LimbToolkit');
Mock::generate('request');
Mock::generate('fetcher');
Mock::generate('validator');
Mock::generate('dataspace');

Mock::generatePartial(
                      'form_create_site_object_command',
                      'form_create_site_object_command_test_version',
                      array('_is_first_time', '_get_validator')
                      );

class form_create_site_object_command_test extends LimbTestCase 
{
	var $command;
  var $request;
  var $toolkit;
  var $fetcher;
  var $validator;
  var $dataspace;
		  	
  function setUp()
  {
    $this->request = new Mockrequest($this);
    $this->fetcher = new Mockfetcher($this);
    $this->validator = new Mockvalidator($this);
    $this->dataspace = new Mockdataspace($this); 
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getFetcher', $this->fetcher);
    $this->toolkit->setReturnValue('getRequest', $this->request);
    $this->toolkit->setReturnValue('getDataspace', $this->dataspace);
     
    Limb :: registerToolkit($this->toolkit);
    
  	$this->command = new form_create_site_object_command_test_version($this);
    $this->command->__construct('test_form');
    
    $this->command->setReturnValue('_is_first_time', false);
    $this->command->setReturnValue('_get_validator', $this->validator);
    $this->validator->setReturnValue('validate', true);
  }
  
  function tearDown()
  { 
    Limb :: popToolkit();
    
    $this->request->tally();
    $this->fetcher->tally();
  	$this->toolkit->tally();
    $this->validator->tally();
    $this->dataspace->tally();
  }
          
  function test_register_validation_rules_no_parent_node_id()
  {	
    $object_data = array('parent_node_id' => 100);
    
    $this->dataspace->expectOnce('get', array('parent_node_id'));
    $this->dataspace->setReturnValue('get', null);
    
  	$this->fetcher->expectOnce('fetch_requested_object', array(new IsAExpectation('Mockrequest')));
  	$this->fetcher->setReturnValue('fetch_requested_object', $object_data);
    
    $this->validator->expectCallCount('add_rule', 2);
    $this->validator->expectArgumentsAt(0, 'add_rule', array(array(LIMB_DIR . 'class/validators/rules/tree_node_id_rule', 'parent_node_id')));
    $this->validator->expectArgumentsAt(1, 'add_rule', array(array(LIMB_DIR . 'class/validators/rules/tree_identifier_rule', 'identifier', 100)));
     
    $this->command->perform();
  }
  
  function test_register_validation_rules()
  {	
    $this->dataspace->expectOnce('get', array('parent_node_id'));
    $this->dataspace->setReturnValue('get', 100);
    
  	$this->fetcher->expectNever('fetch_requested_object');
    
    $this->validator->expectCallCount('add_rule', 2);
    $this->validator->expectArgumentsAt(0, 'add_rule', array(array(LIMB_DIR . 'class/validators/rules/tree_node_id_rule', 'parent_node_id')));
    $this->validator->expectArgumentsAt(1, 'add_rule', array(array(LIMB_DIR . 'class/validators/rules/tree_identifier_rule', 'identifier', 100)));
     
    $this->command->perform();
  }
  
}

?>