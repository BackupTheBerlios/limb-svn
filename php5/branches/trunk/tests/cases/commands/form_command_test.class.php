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

Mock :: generatePartial(
  'form_command', 
  'form_command_test_version',
  array('_get_validator', 
        '_register_validation_rules', 
        '_init_first_time_dataspace',
        '_define_datamap')
);

Mock :: generate('LimbToolkit');
Mock :: generate('request');
Mock :: generate('validator');
Mock :: generate('dataspace');

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
    $this->toolkit->setReturnValue('switchDataspace', $this->dataspace, array('test_form'));
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
  	$this->form_command->__construct('test_form');
    
    $this->request->expectOnce('get');
    $this->request->setReturnValue('get', array('submitted' => 0), array('test_form'));
    $this->form_command->expectOnce('_init_first_time_dataspace', 
                                    array(new IsAExpectation('Mockdataspace'),
                                          new IsAExpectation('Mockrequest')
                                          ));
  	$this->assertEqual($this->form_command->perform(), Limb :: STATUS_FORM_DISPLAYED);
  }
  
  function test_validation_succeed_on_submit() 	
  {
    $this->form_command->__construct('test_form');
    
    $this->request->setReturnValue('get', $request = array('test' => 1, 'submitted' => 1), array('test_form'));
    
    $this->form_command->setReturnValue('_define_datamap', array('test' => 'test2'));
    
    $this->dataspace->expectOnce('merge', array(array('test2' => 1)));
    
  	$this->form_command->expectOnce('_register_validation_rules');

   	$this->validator->expectOnce('validate');
  	$this->validator->setReturnValue('validate', true);

  	$this->assertEqual($this->form_command->perform(), Limb :: STATUS_FORM_SUBMITTED);
  }
  
  function test_validation_failed_on_submit() 	
  {
    $this->form_command->__construct('test_form');
    
    $this->request->setReturnValue('get', $request = array('test' => 1, 'submitted' => 1), array('test_form'));
    
    $this->form_command->setReturnValue('_define_datamap', array('test' => 'test2'));
    
    $this->dataspace->expectOnce('merge', array(array('test2' => 1)));
    
  	$this->form_command->expectOnce('_register_validation_rules');

   	$this->validator->expectOnce('validate');
  	$this->validator->setReturnValue('validate', false);

  	$this->assertEqual($this->form_command->perform(), Limb :: STATUS_FORM_NOT_VALID);
  }
}

?>