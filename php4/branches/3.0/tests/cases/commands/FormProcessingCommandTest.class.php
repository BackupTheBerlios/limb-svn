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
require_once(LIMB_DIR . '/core/commands/FormProcessingCommand.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/template/components/form/form.inc.php');

Mock :: generate('LimbBaseToolkit', 'MockLimbToolkit');
Mock :: generate('Request');
Mock :: generate('Dataspace');
Mock :: generate('FormComponent');

class FormProcessingCommandTest extends LimbTestCase
{
  var $dataspace;
  var $request;
  var $toolkit;
  var $form_id = 'test_form';
  var $form_component;

  function FormProcessingCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->dataspace = new MockDataspace($this);
    $this->request = new MockRequest($this);

    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnReference('getRequest', $this->request);

    $view = new Component();
    $this->toolkit->expectOnce('getView');
    $this->toolkit->setReturnReference('getView', $view);

    $this->form_component = new MockFormComponent($this);
    $view->addChild($this->form_component, $this->form_id);

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    Limb :: restoreToolkit();

    $this->dataspace->tally();
    $this->request->tally();
    $this->toolkit->tally();
    $this->form_component->tally();
  }

  function testMultiFormDisplay()
  {
    $form_command = new FormProcessingCommand($form_id = 'test_form', LIMB_MULTI_FORM, array());

    $this->toolkit->setReturnReference('switchDataspace', $this->dataspace, array($form_id));

    $this->request->expectOnce('get');
    $this->request->setReturnValue('get', array('submitted' => 0), array($form_id));

    $this->form_component->expectOnce('registerDataSource', array($this->dataspace));

    $this->assertEqual($form_command->perform(), LIMB_STATUS_FORM_DISPLAYED);
  }

  function testSingleFormDisplay()
  {
    $form_command = new FormProcessingCommand($form_id = 'test_form', LIMB_SINGLE_FORM, array());

    $this->toolkit->setReturnReference('getDataspace', $this->dataspace);

    $this->request->expectOnce('export');
    $this->request->setReturnValue('export', array('submitted' => 0));

    $this->form_component->expectOnce('registerDataSource', array($this->dataspace));

    $this->assertEqual($form_command->perform(), LIMB_STATUS_FORM_DISPLAYED);
  }

  function testMultiFormSubmit()
  {
    $map = array('request_field1' => 'dataspace_field1',
                 'request_field2' => 'dataspace_field2');

    $form_command = new FormProcessingCommand($form_id = 'test_form', LIMB_MULTI_FORM, $map);

    $this->toolkit->setReturnReference('switchDataspace', $this->dataspace, array('test_form'));

    $this->request->setReturnValue('get',
                                   $request = array('request_field1' => 1,
                                                    'submitted' => 1),
                                   array($form_id));

    $this->dataspace->expectOnce('merge', array(array('dataspace_field1' => 1)));

    $this->form_component->expectOnce('registerDataSource', array($this->dataspace));

    $this->assertEqual($form_command->perform(), LIMB_STATUS_FORM_SUBMITTED);
  }

  function testSingleFormSubmit()
  {
    $map = array('request_field1' => 'dataspace_field1',
                 'request_field2' => 'dataspace_field2');

    $form_command = new FormProcessingCommand($form_id = 'test_form', LIMB_SINGLE_FORM, $map);

    $this->toolkit->setReturnReference('getDataspace', $this->dataspace);

    $this->request->setReturnValue('export',
                                   $request = array('request_field1' => 1,
                                                    'request_field2' => 10,
                                                    'junk_field' => 100,
                                                    'submitted' => 1));

    $this->dataspace->expectOnce('merge', array(array('dataspace_field1' => 1,
                                                      'dataspace_field2' => 10,
                                                      )));

    $this->form_component->expectOnce('registerDataSource', array($this->dataspace));

    $this->assertEqual($form_command->perform(), LIMB_STATUS_FORM_SUBMITTED);
  }
}

?>