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
require_once(LIMB_DIR . '/core/commands/FormValidateCommand.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');

require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/template/components/form/form.inc.php');
require_once(WACT_ROOT . '/validation/validator.inc.php');
require_once(WACT_ROOT . '/validation/errorlist.inc.php');

Mock :: generate('LimbBaseToolkit');
Mock :: generate('Validator');
Mock :: generate('FormComponent');

class FormValidateCommandTest extends LimbTestCase
{
  var $toolkit;

  function FormValidateCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->toolkit = new MockLimbBaseToolkit($this);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    Limb :: restoreToolkit();

    $this->toolkit->tally();
  }


  function testPerformValidateOk()
  {
    $validator = new MockValidator($this);

    $form_command = new FormValidateCommand($form_id = 'test_form', $validator);

    $dataspace = new Dataspace();
    $this->toolkit->expectOnce('getDataspace');
    $this->toolkit->setReturnReference('getDataspace', $dataspace);

    $validator->expectOnce('validate', array($dataspace));
    $validator->expectOnce('isValid');
    $validator->setReturnValue('isValid', true);

    $this->assertEqual($form_command->perform(), LIMB_STATUS_OK);

    $validator->tally();
  }

  function testPerformNotValid()
  {
    $validator = new MockValidator($this);

    $form_command = new FormValidateCommand($form_id = 'test_form', $validator);

    $dataspace = new Dataspace();
    $this->toolkit->expectOnce('getDataspace');
    $this->toolkit->setReturnReference('getDataspace', $dataspace);

    // Validator is not valid
    $validator->expectOnce('validate', array($dataspace));
    $validator->expectOnce('isValid');
    $validator->setReturnValue('isValid', false);

    $form_component = new MockFormComponent($this);
    $view = new Component();

    // We have to transfer error list and dataspace to the form component
    $error_list = new ErrorList();
    $validator->expectOnce('getErrorList');
    $validator->setReturnReference('getErrorList', $error_list);

    $this->toolkit->expectOnce('getView');
    $this->toolkit->setReturnReference('getView', $view);

    $view->addChild($form_component, $form_id);

    $form_component->expectOnce('setErrors', array($error_list));
    $form_component->expectOnce('registerDataSource', array($dataspace));

    $this->assertEqual($form_command->perform(), LIMB_STATUS_FORM_NOT_VALID);

    $form_component->tally();
    $validator->tally();
  }
}

?>