<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/core/commands/FormCommand.class.php');
require_once(LIMB_DIR . '/class/core/request/Request.class.php');
require_once(LIMB_DIR . '/class/core/Dataspace.class.php');
require_once(LIMB_DIR . '/class/validators/Validator.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');

Mock :: generatePartial(
  'FormCommand',
  'FormCommandTestVersion',
  array('_getValidator',
        '_registerValidationRules',
        '_initFirstTimeDataspace',
        '_defineDatamap')
);

Mock :: generate('LimbToolkit');
Mock :: generate('Request');
Mock :: generate('Validator');
Mock :: generate('Dataspace');

class FormCommandTest extends LimbTestCase
{
  var $form_command;
  var $dataspace;
  var $request;
  var $toolkit;
  var $validator;

  function setUp()
  {
    $this->dataspace = new MockDataspace($this);
    $this->request = new MockRequest($this);
    $this->validator = new MockValidator($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('switchDataspace', $this->dataspace, array('test_form'));
    $this->toolkit->setReturnReference('getRequest', $this->request);

    Limb :: registerToolkit($this->toolkit);

    $this->form_command = new FormCommandTestVersion($this);
    $this->form_command->setReturnReference('_getValidator', $this->validator);
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

  function testFormDisplayedStatus()
  {
    $this->form_command->FormCommand('test_form');

    $this->request->expectOnce('get');
    $this->request->setReturnValue('get', array('submitted' => 0), array('testForm'));
    $this->form_command->expectOnce('_initFirstTimeDataspace',
                                    array(new IsAExpectation('MockDataspace'),
                                          new IsAExpectation('MockRequest')
                                          ));
    $this->assertEqual($this->form_command->perform(), LIMB_STATUS_FORM_DISPLAYED);
  }

  function testValidationSucceedOnSubmit()
  {
    $this->form_command->FormCommand('test_form');

    $this->request->setReturnValue('get', $request = array('test' => 1, 'submitted' => 1), array('testForm'));

    $this->form_command->setReturnValue('_defineDatamap', array('test' => 'test2'));

    $this->dataspace->expectOnce('merge', array(array('test2' => 1)));

    $this->form_command->expectOnce('_registerValidationRules');

    $this->validator->expectOnce('validate');
    $this->validator->setReturnValue('validate', true);

    $this->assertEqual($this->form_command->perform(), LIMB_STATUS_FORM_SUBMITTED);
  }

  function testValidationFailedOnSubmit()
  {
    $this->form_command->FormCommand('test_form');

    $this->request->setReturnValue('get', $request = array('test' => 1, 'submitted' => 1), array('testForm'));

    $this->form_command->setReturnValue('_defineDatamap', array('test' => 'test2'));

    $this->dataspace->expectOnce('merge', array(array('test2' => 1)));

    $this->form_command->expectOnce('_registerValidationRules');

    $this->validator->expectOnce('validate');
    $this->validator->setReturnValue('validate', false);

    $this->assertEqual($this->form_command->perform(), LIMB_STATUS_FORM_NOT_VALID);
  }
}

?>