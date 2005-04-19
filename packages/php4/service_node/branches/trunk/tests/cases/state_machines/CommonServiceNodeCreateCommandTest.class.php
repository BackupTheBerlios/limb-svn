<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: ImageObjectsDAOTest.class.php 1093 2005-02-07 15:17:20Z pachanga $
*
***********************************************************************************/
require_once(LIMB_SERVICE_NODE_DIR . '/state_machines/CommonServiceNodeCreateCommand.class.php');
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');

class CommonServiceNodeCreateCommandTest extends LimbTestCase
{
  var $db;
  var $command;

  function CommonServiceNodeCreateCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDBConnection();
    $this->db =& new SimpleDB($conn);

    $this->_cleanUp();

    $form_template_path = '/test_content_service_node_form.html';
    $form_name = 'test_form';

    RegisterTestingTemplate($form_template_path, '<form id="' . $form_name . '" runat="server"></form>');

    // dataspace field => value
    $extra_dataspace_data = array('service_name' => 'SomeService');

    $this->command = new CommonServiceNodeCreateCommand($form_template_path,
                                                         $form_name,
                                                         $extra_dataspace_data);

    Limb :: saveToolkit();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();

    ClearTestingTemplates();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_uid');
    $this->db->delete('sys_object');
    $this->db->delete('sys_object_to_service');
    $this->db->delete('sys_class');
    $this->db->delete('sys_service');
    $this->db->delete('sys_tree');
    $this->db->delete('sys_object_to_node');
  }

  function _registerRootObject()
  {
    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();

    $uow->start();

    $entity = new ServiceNode();

    $node =& $entity->getPart('node');
    $node->set('identifier', 'services');

    $service =& $entity->getPart('service');
    $service->set('title', 'Services page');
    $service->set('name', 'ServiceNode');

    $uow->register($entity);
    $uow->commit();

    return $entity;
  }

  function testPerformInitFailed()
  {
    $this->assertEqual($this->command->perform(new DataSpace()), LIMB_STATUS_OK);

    $this->assertEqual($this->command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('form' => LIMB_STATUS_FORM_DISPLAYED),
                                                     array('init' => LIMB_STATUS_ERROR),
                                                     array('error' => LIMB_STATUS_OK),
                                                     array('render' => LIMB_STATUS_OK),
                                                     ));
  }

  function testPerformFormDisplayed()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $toolkit->setCurrentEntity($entity);

    $this->assertEqual($this->command->perform(new DataSpace()), LIMB_STATUS_OK);

    $this->assertEqual($this->command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('form' => LIMB_STATUS_FORM_DISPLAYED),
                                                     array('init' => LIMB_STATUS_OK),
                                                     array('render' => LIMB_STATUS_OK),
                                                     ));
  }

  function testPerformFormNotValid()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();
    $toolkit->setCurrentEntity($entity);
    $request->set('submitted', 1);

    $this->assertEqual($this->command->perform(new DataSpace()), LIMB_STATUS_OK);

    $this->assertEqual($this->command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('form' => LIMB_STATUS_FORM_SUBMITTED),
                                                     array('extra_data' => LIMB_STATUS_OK),
                                                     array('validate' => LIMB_STATUS_FORM_NOT_VALID),
                                                     array('render' => LIMB_STATUS_OK),
                                                     ));
  }

  function testPerformObjectCreated()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();
    $toolkit->setCurrentEntity($entity);
    $request =& $toolkit->getRequest();

    $node =& $entity->getPart('node');
    $request->set('parent_node_id', $node->get('id'));
    $request->set('submitted', 1);
    $request->set('identifier', $identifier = 'child_service');
    $request->set('title', $title = 'Some title');

    $context = new DataSpace();

    $this->assertEqual($this->command->perform($context), LIMB_STATUS_OK);

    $this->assertEqual($this->command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('form' => LIMB_STATUS_FORM_SUBMITTED),
                                                     array('extra_data' => LIMB_STATUS_OK),
                                                     array('validate' => LIMB_STATUS_OK),
                                                     array('new_object' => LIMB_STATUS_OK),
                                                     array('map_to_object' => LIMB_STATUS_OK),
                                                     array('register_object' => LIMB_STATUS_OK),
                                                     array('redirect' => LIMB_STATUS_OK),
                                                     ));

    $this->assertIsA($context->getObject('entity'), 'ServiceNode');
    $entity =& $context->getObject('entity');

    $response =& $toolkit->getResponse();
    $this->assertTrue($response->isRedirected());
  }
}

?>