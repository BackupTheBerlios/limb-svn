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
require_once(LIMB_DIR . '/class/core/limb_toolkit.interface.php');
require_once(LIMB_DIR . '/class/validators/validator.class.php');
require_once(LIMB_DIR . '/class/core/datasources/requested_object_datasource.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('request');
Mock :: generate('requested_object_datasource');
Mock :: generate('validator');
Mock :: generate('dataspace');

Mock :: generatePartial(
                      'form_create_site_object_command',
                      'form_create_site_object_command_test_version',
                      array('_is_first_time', '_get_validator')
                      );

class form_create_site_object_command_test extends LimbTestCase 
{
	var $command;
  var $request;
  var $toolkit;
  var $datasource;
  var $validator;
  var $dataspace;
		  	
  function setUp()
  {
    $this->request = new Mockrequest($this);
    $this->datasource = new Mockrequested_object_datasource($this);
    $this->validator = new Mockvalidator($this);
    $this->dataspace = new Mockdataspace($this); 
    
    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getDatasource', $this->datasource, array('requested_object_datasource'));
    $this->toolkit->setReturnValue('getRequest', $this->request);
    $this->toolkit->setReturnValue('switchDataspace', $this->dataspace, array('test_form'));
     
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
    $this->datasource->tally();
  	$this->toolkit->tally();
    $this->validator->tally();
    $this->dataspace->tally();
  }
          
  function test_register_validation_rules_no_parent_node_id()
  {	
    $object_data = array('parent_node_id' => 100);
    
    $this->dataspace->expectOnce('get', array('parent_node_id'));
    $this->dataspace->setReturnValue('get', null);

    $this->datasource->expectOnce('set_request', array(new IsAExpectation('Mockrequest')));
    $this->datasource->expectOnce('fetch');    
  	$this->datasource->setReturnValue('fetch', $object_data);
    
    $this->validator->expectCallCount('add_rule', 2);
    $this->validator->expectArgumentsAt(0, 'add_rule', array(array(LIMB_DIR . '/class/validators/rules/tree_node_id_rule', 'parent_node_id')));
    $this->validator->expectArgumentsAt(1, 'add_rule', array(array(LIMB_DIR . '/class/validators/rules/tree_identifier_rule', 'identifier', 100)));
     
    $this->command->perform();
  }
  
  function test_register_validation_rules()
  {	
    $this->dataspace->expectOnce('get', array('parent_node_id'));
    $this->dataspace->setReturnValue('get', 100);
    
  	$this->datasource->expectNever('fetch');
    
    $this->validator->expectCallCount('add_rule', 2);
    $this->validator->expectArgumentsAt(0, 'add_rule', array(array(LIMB_DIR . '/class/validators/rules/tree_node_id_rule', 'parent_node_id')));
    $this->validator->expectArgumentsAt(1, 'add_rule', array(array(LIMB_DIR . '/class/validators/rules/tree_identifier_rule', 'identifier', 100)));
     
    $this->command->perform();
  }
  
}

?>