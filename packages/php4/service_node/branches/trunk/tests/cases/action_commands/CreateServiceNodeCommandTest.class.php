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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/CreateServiceNodeCommand.class.php');
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');
require_once(LIMB_DIR . '/core/request_resolvers/TreeBasedEntityRequestResolver.class.php');

class CreateServiceNodeCommandTest extends LimbTestCase
{
  var $db;
  var $command;

  function CreateServiceNodeCommandTest()
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

    $validator =  new LimbHandle(LIMB_SERVICE_NODE_DIR . '/validators/CommonCreateServiceNodeValidator');

    $this->command = new CreateServiceNodeCommand($form_template_path,
                                                  $form_name,
                                                  $validator,
                                                  array(),
                                                  $extra_dataspace_data,
                                                  new ServiceNode());

    $toolkit =& Limb :: saveToolkit();
    $toolkit->setRequestResolver('tree_based_entity', new TreeBasedEntityRequestResolver());

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

  function & _registerRootObject()
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

  function testPerformFormDisplayed()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath('/services');

    $this->assertEqual($this->command->perform(), LIMB_STATUS_OK);
  }

  function testPerformFormNotValid()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();

    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath('/services');
    $request->set('submitted', 1);

    $this->assertEqual($this->command->perform(), LIMB_STATUS_OK);
  }

  function testPerformObjectCreated()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath('/services');

    $node =& $entity->getNodePart();
    $request->set('parent_node_id', $node->get('id'));
    $request->set('submitted', 1);
    $request->set('identifier', $identifier = 'child_service');
    $request->set('title', $title = 'Some title');

    $this->assertEqual($this->command->perform(), LIMB_STATUS_OK);

    $service_node =& $this->command->getEntity();
    $this->assertIsA($service_node, 'ServiceNode');

    $response =& $toolkit->getResponse();
    $this->assertTrue($response->isRedirected());

    $uow =& $toolkit->getUOW();
    $this->assertTrue($uow->isRegistered($service_node));

  }
}

?>