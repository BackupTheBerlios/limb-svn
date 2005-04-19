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
require_once(LIMB_SERVICE_NODE_DIR . '/state_machines/CommonServiceNodeEditCommand.class.php');
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');

class CommonServiceNodeEditCommandTest extends LimbTestCase
{
  var $db;
  var $command;

  function CommonServiceNodeEditCommandTest()
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

    $this->command = new CommonServiceNodeEditCommand($form_template_path,
                                                      $form_name);

    Limb :: saveToolkit();
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

  function tearDown()
  {
    Limb :: restoreToolkit();

    clearTestingTemplates();

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

  function testPerformEntityNotFound()
  {
    $context = new DataSpace();

    $this->assertEqual($this->command->perform($context), LIMB_STATUS_OK);

    $this->assertNull($context->getObject('service_node'));

    $this->assertEqual($this->command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('init_service_node' => LIMB_STATUS_ERROR),
                                                     array('error' =>  LIMB_STATUS_OK),
                                                     array('render' =>  LIMB_STATUS_OK)));
  }

  function testPerformFormDisplayed()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();
    $toolkit->setCurrentEntity($entity);
    $request =& $toolkit->getRequest();

    $context = new DataSpace();

    $this->assertEqual($this->command->perform($context), LIMB_STATUS_OK);

    $this->assertEqual($this->command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('init_service_node' => LIMB_STATUS_OK),
                                                     array('form' => LIMB_STATUS_FORM_DISPLAYED),
                                                     array('map_to_dataspace' => LIMB_STATUS_OK),
                                                     array('render' => LIMB_STATUS_OK),
                                                     ));

    $this->assertIsA($context->getObject('entity'), 'ServiceNode');

    $this->assertEqual($context->getObject('entity'), $entity);
  }

  function testPerformFormNotValid()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();
    $toolkit->setCurrentEntity($entity);
    $request =& $toolkit->getRequest();
    $request->set('submitted', 1);

    $context = new DataSpace();

    $this->assertEqual($this->command->perform($context), LIMB_STATUS_OK);

    $this->assertEqual($this->command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('init_service_node' => LIMB_STATUS_OK),
                                                     array('form' => LIMB_STATUS_FORM_SUBMITTED),
                                                     array('validate' => LIMB_STATUS_FORM_NOT_VALID),
                                                     array('render' => LIMB_STATUS_OK),
                                                     ));
  }

  function testPerformServiceNodeEdited()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();
    $toolkit->setCurrentEntity($entity);
    $request =& $toolkit->getRequest();

    $node =& $entity->getPart('node');
    $request->set('parent_node_id', $node->get('id'));
    $request->set('submitted', 1);
    $request->set('title', $title = 'Other title');
    $request->set('identifier', $identifier = 'other_identifier');

    $context = new DataSpace();

    $this->assertEqual($this->command->perform($context), LIMB_STATUS_OK);

    $this->assertEqual($this->command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('init_service_node' => LIMB_STATUS_OK),
                                                     array('form' => LIMB_STATUS_FORM_SUBMITTED),
                                                     array('validate' => LIMB_STATUS_OK),
                                                     array('map_to_service_node' => LIMB_STATUS_OK),
                                                     array('redirect' => LIMB_STATUS_OK),
                                                     ));

    $entity =& $context->getObject('entity');
    $node =& $entity->getPart('node');
    $service =& $entity->getPart('service');
  }
}

?>