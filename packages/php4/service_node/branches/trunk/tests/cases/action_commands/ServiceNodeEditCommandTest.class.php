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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/ServiceNodeEditCommand.class.php');
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');
require_once(LIMB_DIR . '/core/request_resolvers/TreeBasedEntityRequestResolver.class.php');

class ServiceNodeEditCommandTest extends LimbTestCase
{
  var $db;
  var $command;

  function ServiceNodeEditCommandTest()
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

    $validator =  new LimbHandle(LIMB_SERVICE_NODE_DIR . '/validators/CommonEditServiceNodeValidator');

    $this->command = new ServiceNodeEditCommand($form_template_path,
                                                $form_name,
                                                $validator);

    Limb :: saveToolkit();

    $toolkit =& Limb :: toolkit();
    $toolkit->setRequestResolver('tree_based_entity', new TreeBasedEntityRequestResolver());
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

  function testPerformFormDisplayed()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath('/services');

    $this->assertEqual($this->command->perform(), LIMB_STATUS_OK);

    $service_node =& $this->command->getServiceNode();
    $this->assertIsA($service_node, 'ServiceNode');
    $this->assertEqual($service_node, $entity);
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

    $this->assertTrue($this->_isFormHasErrors('test_form'));
  }

  function testPerformServiceNodeEdited()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath('/services');

    $node =& $entity->getPart('node');
    $request->set('parent_node_id', $node->get('id'));
    $request->set('submitted', 1);
    $request->set('title', $title = 'Other title');
    $request->set('identifier', $identifier = 'other_identifier');

    $this->assertEqual($this->command->perform(), LIMB_STATUS_OK);

    $entity =& $this->command->getServiceNode();

    $node =& $entity->getPart('node');
    $this->assertEqual($node->get('identifier'), $identifier);

    $service =& $entity->getPart('service');
    $this->assertEqual($service->get('title'), $title);
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