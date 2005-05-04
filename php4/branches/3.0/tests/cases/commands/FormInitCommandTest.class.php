<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: FormInitCommandTest.class.php 1215 2005-04-12 14:35:01Z seregalimb $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/commands/FormInitCommand.class.php');
require_once(LIMB_DIR . '/core/request/Request.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/template/components/form/form.inc.php');

Mock :: generate('FormComponent');

class FormInitCommandTest extends LimbTestCase
{
  var $form_id = 'test_form';
  var $form_component;

  function FormInitCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $view = new Component();
    $this->form_component = new MockFormComponent($this);
    $view->addChild($this->form_component, $this->form_id);

    Limb :: saveToolkit();

    $toolkit =& Limb :: toolkit();
    $toolkit->setView($view);
  }

  function tearDown()
  {
    Limb :: restoreToolkit();

    $this->form_component->tally();
  }

  function testMultiFormDisplay()
  {
    $form_command = new FormInitCommand($this->form_id, LIMB_MULTI_FORM);

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->switchDataspace($this->form_id);
    $request =& $toolkit->getRequest();

    $data = array('submitted' => 0);
    $request->set($this->form_id, $data);

    $this->form_component->expectOnce('registerDataSource', array($dataspace));

    $this->assertEqual($form_command->perform(), LIMB_STATUS_FORM_DISPLAYED);
  }

  function testSingleFormDisplay()
  {
    $form_command = new FormInitCommand($this->form_id, LIMB_SINGLE_FORM);

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->switchDataspace($this->form_id);
    $request =& $toolkit->getRequest();

    $data = array('submitted' => 0);
    $request->set('submitted', 0);

    $this->form_component->expectOnce('registerDataSource', array($dataspace));

    $this->assertEqual($form_command->perform(), LIMB_STATUS_FORM_DISPLAYED);
  }

  function testMultiFormSubmit()
  {
    $form_command = new FormInitCommand($this->form_id, LIMB_MULTI_FORM);

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->switchDataspace($this->form_id);
    $request =& $toolkit->getRequest();

    $data = array('submitted' => 1,
                  'request_field1' => 1);

    $request->set($this->form_id, $data);

    $this->form_component->expectOnce('registerDataSource', array($dataspace));

    $this->assertEqual($form_command->perform(), LIMB_STATUS_FORM_SUBMITTED);

    $this->assertEqual($dataspace->get('request_field1'), 1);
    $this->assertEqual($dataspace->get('submitted'), 1);
  }

  function testSingleFormSubmit()
  {
    $form_command = new FormInitCommand($this->form_id, LIMB_SINGLE_FORM);

    $toolkit =& Limb :: toolkit();
    $dataspace =& $toolkit->switchDataspace($this->form_id);
    $request =& $toolkit->getRequest();

    $request->set('submitted', 2);
    $request->set('request_field1', 2);

    $this->form_component->expectOnce('registerDataSource', array($dataspace));

    $this->assertEqual($form_command->perform(), LIMB_STATUS_FORM_SUBMITTED);
    $this->assertEqual($dataspace->get('request_field1'), 2);
    $this->assertEqual($dataspace->get('submitted'), 2);

  }
}

?>