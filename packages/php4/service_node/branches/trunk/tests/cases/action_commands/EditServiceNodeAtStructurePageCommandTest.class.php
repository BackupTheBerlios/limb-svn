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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/EditServiceNodeAtStructurePageCommand.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/request_resolvers/ServiceNodeRequestResolver.class.php');

class EditServiceNodeAtStructurePageCommandTest extends LimbTestCase
{
  var $db;

  function EditServiceNodeAtStructurePageCommandTest()
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

  function & _registerServiceNode()
  {
    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();

    $uow->start();

    $entity = new ServiceNode();

    $node =& $entity->getNodePart();
    $node->set('identifier', 'services');

    $service =& $entity->getServicePart();
    $service->set('title', 'Services page');
    $service->set('name', 'ServiceNode');

    $uow->register($entity);
    $uow->commit();

    return $entity;
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

  function testPerformFormDisplayed()
  {
    $entity =& $this->_registerServiceNode();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $entity->get('oid'));

    $command = new EditServiceNodeAtStructurePageCommand();

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $service_node =& $command->getEntity();
    $this->assertIsA($service_node, 'ServiceNode');
    $this->assertEqual($service_node->get('oid'), $entity->get('oid'));
  }

  function testPerformFormNotValid()
  {
    $entity =& $this->_registerServiceNode();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $entity->get('oid'));
    $request->set('submitted', 1);

    $command = new EditServiceNodeAtStructurePageCommand();

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $this->assertTrue($this->_isFormHasErrors('service_node_form'));
  }

  function testPerformServiceNodeEdited()
  {
    $entity =& $this->_registerServiceNode();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $entity->get('oid'));
    $request->set('submitted', 1);
    $request->set('title', $title = 'Other title');
    $request->set('identifier', $identifier = 'other_identifier');
    $request->set('service_name', 'ServiceNode');

    $context = new DataSpace();

    $command = new EditServiceNodeAtStructurePageCommand();

    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $service_node =& $command->getEntity();
    $node =& $service_node->getNodePart();
    $service =& $service_node->getServicePart();

    $this->assertEqual($service->get('title'), $title);
    $this->assertEqual($node->get('identifier'), $identifier);

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