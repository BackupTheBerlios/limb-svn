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
require_once(LIMB_DIR . '/class/core/commands/form_command.class.php');
require_once(LIMB_DIR . '/class/core/request/request.class.php');
require_once(LIMB_DIR . '/class/core/dataspace.class.php');
require_once(LIMB_DIR . '/class/validators/validator.class.php');
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');

Mock::generatePartial(
  'form_command', 
  'form_command_test_version',
  array('_get_validator', '_register_validation_rules')
);

Mock::generate('LimbToolkit');
Mock::generate('request');
Mock::generate('validator');
Mock::generate('dataspace');

class form_command_test extends LimbTestCase 
{
	var $form_command;
	var $dataspace;
  var $request;
  var $toolkit;
  var $validator;
		  	
  function setUp()
  {
    $this->dataspace = new Mockdataspace($this);
    $this->request = new Mockrequest($this);
    $this->validator = new Mockvalidator($this);
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getDataspace', $this->dataspace);
    $this->toolkit->setReturnValue('getRequest', $this->request);
     
    Limb :: registerToolkit($this->toolkit);
    
  	$this->form_command = new form_command_test_version($this);
    $this->form_command->setReturnValue('_get_validator', $this->validator);
  }
  
  function tearDown()
  { 
    Limb :: popToolkit();
    
  	$this->dataspace->tally();
    $this->request->tally();
    $this->validator->tally();
  	$this->toolkit->tally();
  	$this->form_command->tally();
  }
          
  function test_form_displayed_status()
  {	
    $this->dataspace->setReturnValue('get', 0, array('submitted'));
    $this->dataspace->expectOnce('merge', array($test_data = array('test' => 1)));
    
  	$this->form_command->__construct('test_form');

  	$this->request->expectOnce('get');
  	$this->request->setReturnValue('get', $test_data, array('test_form'));
  
  	$this->assertEqual($this->form_command->perform(), Limb :: STATUS_FORM_DISPLAYED);
  }
  
  function test_validation_succeed_on_submit() 	
  {
    $this->dataspace->setReturnValue('get', 1, array('submitted'));
    $this->dataspace->expectOnce('merge', array($test_data = array('test' => 1)));
    
  	$this->form_command->__construct('test_form');
  	$this->form_command->expectOnce('_register_validation_rules');

  	$this->request->expectOnce('get');
  	$this->request->setReturnValue('get', $test_data, array('test_form'));

   	$this->validator->expectOnce('validate');
  	$this->validator->setReturnValue('validate', true);

  	$this->assertEqual($this->form_command->perform(), Limb :: STATUS_FORM_SUBMITTED);
  }

  function test_validation_failed_on_submit() 	
  {
    $this->dataspace->setReturnValue('get', 1, array('submitted'));
    $this->dataspace->expectOnce('merge', array($test_data = array('test' => 1)));
    
  	$this->form_command->__construct('test_form');
  	$this->form_command->expectOnce('_register_validation_rules');

  	$this->request->expectOnce('get');
  	$this->request->setReturnValue('get', $test_data, array('test_form'));

   	$this->validator->expectOnce('validate');
  	$this->validator->setReturnValue('validate', false);

  	$this->assertEqual($this->form_command->perform(), Limb :: STATUS_FORM_NOT_VALID);
  }
}

?>