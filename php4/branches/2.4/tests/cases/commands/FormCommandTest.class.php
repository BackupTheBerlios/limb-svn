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
require_once(LIMB_DIR . '/core/commands/FormCommand.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/LimbToolkit.interface.php');

require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/template/components/form/form.inc.php');
require_once(WACT_ROOT . '/validation/validator.inc.php');
require_once(WACT_ROOT . '/validation/errorlist.inc.php');

Mock :: generatePartial(
  'FormCommand',
  'FormCommandTestVersion',
  array('_getValidator',
        '_registerValidationRules',
        '_initFirstTimeDataspace',
        '_defineDatamap',
        'getFormComponent')
);

Mock :: generate('LimbToolkit');
Mock :: generate('Request');
Mock :: generate('Validator');
Mock :: generate('ErrorList');
Mock :: generate('Dataspace');
Mock :: generate('Component');
Mock :: generate('FormComponent');

class FormCommandTest extends LimbTestCase
{
  var $form_command;
  var $form_component;
  var $dataspace;
  var $request;
  var $toolkit;
  var $validator;

  function FormCommandTest()
  {
    parent :: LimbTestCase('form cmd test');
  }

  function setUp()
  {
    $this->dataspace = new MockDataspace($this);
    $this->request = new MockRequest($this);
    $this->validator = new MockValidator($this);
    $this->form_component = new MockFormComponent($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('getRequest', $this->request);

    Limb :: registerToolkit($this->toolkit);

    $this->form_command = new FormCommandTestVersion($this);
    $this->form_command->setReturnReference('_getValidator', $this->validator);
    $this->form_command->setReturnReference('getFormComponent', $this->form_component);
  }

  function tearDown()
  {
    Limb :: popToolkit();

    $this->dataspace->tally();
    $this->request->tally();
    $this->validator->tally();
    $this->toolkit->tally();
    $this->form_command->tally();
    $this->form_component->tally();
  }

  function testMultiFormDisplayedStatus()
  {
    $this->form_command->FormCommand('test_form', LIMB_MULTI_FORM);

    $this->toolkit->setReturnReference('switchDataspace', $this->dataspace, array('test_form'));

    $this->request->expectOnce('get');
    $this->request->setReturnValue('get', array('submitted' => 0), array('test_form'));
    $this->form_command->expectOnce('_initFirstTimeDataspace',
                                    array(new IsAExpectation('MockDataspace'),
                                          new IsAExpectation('MockRequest')
                                          ));

    $this->form_component->expectOnce('registerDataSource', array(new IsAExpectation('MockDataspace')));

    $this->assertEqual($this->form_command->perform(), LIMB_STATUS_FORM_DISPLAYED);
  }

  function testSingleFormDisplayedStatus()
  {
    $this->form_command->FormCommand('test_form', LIMB_SINGLE_FORM);

    $this->toolkit->setReturnReference('getDataspace', $this->dataspace);

    $this->request->expectOnce('export');
    $this->request->setReturnValue('export', array('submitted' => 0));
    $this->form_command->expectOnce('_initFirstTimeDataspace',
                                    array(new IsAExpectation('MockDataspace'),
                                          new IsAExpectation('MockRequest')
                                          ));

    $this->form_component->expectOnce('registerDataSource', array(new IsAExpectation('MockDataspace')));

    $this->assertEqual($this->form_command->perform(), LIMB_STATUS_FORM_DISPLAYED);
  }

  function testMultiFormValidationSucceedOnSubmit()
  {
    $this->toolkit->setReturnReference('switchDataspace', $this->dataspace, array('test_form'));

    $this->form_command->FormCommand('test_form', LIMB_MULTI_FORM);

    $this->request->setReturnValue('get', $request = array('test' => 1, 'submitted' => 1), array('test_form'));

    $this->form_command->setReturnValue('_defineDatamap', array('test' => 'test2'));

    $this->dataspace->expectOnce('merge', array(array('test2' => 1)));

    $this->form_command->expectOnce('_registerValidationRules');

    $this->form_component->expectOnce('registerDataSource', array(new IsAExpectation('MockDataspace')));
    $this->form_component->expectNever('setErrors');

    $this->validator->expectOnce('validate');
    $this->validator->setReturnValue('IsValid', true);
    $this->validator->expectNever('getErrorList');

    $this->assertEqual($this->form_command->perform(), LIMB_STATUS_FORM_SUBMITTED);
  }

  function testSingleFormValidationSucceedOnSubmit()
  {
    $this->toolkit->setReturnReference('getDataspace', $this->dataspace);

    $this->form_command->FormCommand('test_form', LIMB_SINGLE_FORM);

    $this->request->setReturnValue('export', $request = array('test' => 1, 'submitted' => 1));

    $this->form_command->setReturnValue('_defineDatamap', array('test' => 'test2'));

    $this->dataspace->expectOnce('merge', array(array('test2' => 1)));

    $this->form_command->expectOnce('_registerValidationRules');

    $this->form_component->expectOnce('registerDataSource', array(new IsAExpectation('MockDataspace')));
    $this->form_component->expectNever('setErrors');

    $this->validator->expectOnce('validate');
    $this->validator->setReturnValue('IsValid', true);
    $this->validator->expectNever('getErrorList');

    $this->assertEqual($this->form_command->perform(), LIMB_STATUS_FORM_SUBMITTED);
  }

  function testValidationFailedOnSubmit()
  {
    $this->toolkit->setReturnReference('switchDataspace', $this->dataspace, array('test_form'));

    $this->form_command->FormCommand('test_form', LIMB_MULTI_FORM);

    $this->request->setReturnValue('get', $request = array('test' => 1, 'submitted' => 1), array('test_form'));

    $this->form_command->setReturnValue('_defineDatamap', array('test' => 'test2'));

    $this->dataspace->expectOnce('merge', array(array('test2' => 1)));

    $this->form_command->expectOnce('_registerValidationRules');

    $this->form_component->expectOnce('registerDataSource', array(new IsAExpectation('MockDataspace')));

    $this->validator->expectOnce('validate');
    $this->validator->setReturnValue('IsValid', false);
    $this->validator->expectOnce('getErrorList');
    $this->validator->setReturnValue('getErrorList', $error_list = new MockErrorList($this));

    $this->form_component->expectOnce('setErrors', array(new IsAExpectation('MockErrorList')));

    $this->assertEqual($this->form_command->perform(), LIMB_STATUS_FORM_NOT_VALID);
  }

  function testGetFormComponent()
  {
    $form_command = new FormCommand($form_id = 'test_form');

    $view =& new MockComponent($this);

    $this->toolkit->expectOnce('getView');
    $this->toolkit->setReturnReference('getView', $view);

    $view->expectOnce('findChild', array($form_id));
    $view->setReturnReference('findChild', $this->form_component, array($form_id));

    $this->assertIsA($form_command->getFormComponent(), 'MockFormComponent');

    $view->tally();
  }
}

?>