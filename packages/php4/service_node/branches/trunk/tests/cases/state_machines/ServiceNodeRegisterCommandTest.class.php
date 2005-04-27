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
require_once(LIMB_SERVICE_NODE_DIR . '/state_machines/ServiceNodeRegisterCommand.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/state_machines/TestContentServiceNode.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/state_machines/TestContentServiceNodeMapper.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/request_resolvers/ServiceNodeRequestResolver.class.php');

class ServiceNodeRegisterCommandTest extends LimbTestCase
{
  var $db;

  function ServiceNodeRegisterCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDBConnection();
    $this->db =& new SimpleDB($conn);

    $this->_cleanUp();

    Limb :: saveToolkit();

    $toolkit =& Limb :: toolkit();
    $toolkit->setRequestResolver('service_node', new ServiceNodeRequestResolver());
  }

  function tearDown()
  {
    Limb :: restoreToolkit();

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

  function _registerServiceNode()
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
    $command = new ServiceNodeRegisterCommand();

    $this->assertEqual($command->perform(new DataSpace()), LIMB_STATUS_OK);

    $this->assertEqual($command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('form' => LIMB_STATUS_FORM_DISPLAYED),
                                                     array('init' => LIMB_STATUS_ERROR),
                                                     array('error' => LIMB_STATUS_OK),
                                                     array('render' => LIMB_STATUS_OK),
                                                     ));
  }

  function testPerformFormDisplayed()
  {
    $entity =& $this->_registerServiceNode();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $entity->get('oid'));

    $command = new ServiceNodeRegisterCommand();

    $this->assertEqual($command->perform(new DataSpace()), LIMB_STATUS_OK);

    $this->assertEqual($command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('form' => LIMB_STATUS_FORM_DISPLAYED),
                                                     array('init' => LIMB_STATUS_OK),
                                                     array('render' => LIMB_STATUS_OK),
                                                     ));
  }

  function testPerformFormNotValid()
  {
    $entity =& $this->_registerServiceNode();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $entity->get('oid'));
    $request->set('submitted', 1);

    $command = new ServiceNodeRegisterCommand();

    $this->assertEqual($command->perform(new DataSpace()), LIMB_STATUS_OK);

    $this->assertEqual($command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('form' => LIMB_STATUS_FORM_SUBMITTED),
                                                     array('validate' => LIMB_STATUS_FORM_NOT_VALID),
                                                     array('render' => LIMB_STATUS_OK),
                                                     ));
  }

  function testPerformObjectCreated()
  {
    $entity =& $this->_registerServiceNode();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $entity->get('oid'));
    $request->set('submitted', 1);
    $request->set('identifier', $identifier = 'child_service');
    $request->set('title', $title = 'Some title');
    $request->set('class_name', $class_name = 'TestContentServiceNode');
    $request->set('service_name', $service_name = 'ServiceNode');

    $command = new ServiceNodeRegisterCommand();

    $context = new DataSpace();
    $this->assertEqual($command->perform($context), LIMB_STATUS_OK);

    $this->assertEqual($command->getStateHistory(), array(
                                                     array('initial' => LIMB_STATUS_OK),
                                                     array('form' => LIMB_STATUS_FORM_SUBMITTED),
                                                     array('validate' => LIMB_STATUS_OK),
                                                     array('new_object' => LIMB_STATUS_OK),
                                                     array('map_to_object' => LIMB_STATUS_OK),
                                                     array('register_object' => LIMB_STATUS_OK),
                                                     array('redirect' => LIMB_STATUS_OK),
                                                     ));

    $this->assertIsA($context->getObject('entity'), 'TestContentServiceNode');
    $entity =& $context->getObject('entity');

    $response =& $toolkit->getResponse();
    $this->assertTrue($response->isRedirected());
  }
}

?>