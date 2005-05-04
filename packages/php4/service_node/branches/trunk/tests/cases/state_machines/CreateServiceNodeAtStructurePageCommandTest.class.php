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
require_once(LIMB_SERVICE_NODE_DIR . '/state_machines/CreateServiceNodeAtStructurePageCommand.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/state_machines/TestContentServiceNode.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/state_machines/TestContentServiceNodeMapper.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/request_resolvers/ServiceNodeRequestResolver.class.php');

class CreateServiceNodeAtStructurePageCommandTest extends LimbTestCase
{
  var $db;

  function CreateServiceNodeAtStructurePageCommandTest()
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

  function & _registerServiceNode()
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

  function testPerformFormDisplayed()
  {
    $entity =& $this->_registerServiceNode();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $entity->get('oid'));

    $command = new CreateServiceNodeAtStructurePageCommand();

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $dataspace =& $toolkit->getDataspace();
    $node =& $entity->getPart('node');
    $this->assertEqual($dataspace->get('parent_node_id'), $node->get('id'));
  }

  function testPerformFormNotValid()
  {
    $entity =& $this->_registerServiceNode();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $entity->get('oid'));
    $request->set('submitted', 1);

    $command = new CreateServiceNodeAtStructurePageCommand();

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $this->assertTrue($this->_isFormHasErrors('service_node_form'));
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

    $command = new CreateServiceNodeAtStructurePageCommand();

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $affected_entity =& $command->getAffectedEntity();
    $uow =& $toolkit->getUOW();
    $this->assertTrue($uow->isRegistered($affected_entity));

    $response =& $toolkit->getResponse();
    $this->assertTrue($response->isRedirected());
  }

  function _isFormHasErrors($form_id)
  {
    $toolkit =& Limb :: toolkit();
    $view =& $toolkit->getView();
    $form =& $view->getChild($form_id);
    return $form->hasErrors();
  }
}

?>