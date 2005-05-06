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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/EditContentServiceNodeCommand.class.php');
require_once(LIMB_DIR . '/tests/cases/orm/data_mappers/OneTableObjectMapperTestDbTable.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/action_commands/TestContentServiceNode.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/action_commands/TestContentServiceNodeMapper.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/action_commands/EditTestContentServiceNodeValidator.class.php');
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(LIMB_DIR . '/core/request_resolvers/TreeBasedEntityRequestResolver.class.php');

class EditContentServiceNodeCommandTest extends LimbTestCase
{
  var $db;
  var $command;

  function EditContentServiceNodeCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDBConnection();
    $this->db =& new SimpleDB($conn);

    $this->_cleanUp();

    $template_path = '/test_content_service_node_form.html';
    $form_id = 'test_form';
    $validator = new EditTestContentServiceNodeValidator();

    RegisterTestingTemplate($template_path, '<form id="' . $form_id . '" runat="server"></form>');

    // dataspace field => content part field
    $content_map = array('annotation' => 'annotation',
                         'content' => 'content');

    $this->command = new EditContentServiceNodeCommand($template_path,
                                                       $form_id,
                                                       $validator,
                                                       $content_map);

    Limb :: saveToolkit();

    $toolkit =& Limb :: toolkit();
    $toolkit->setRequestResolver('tree_based_entity', new TreeBasedEntityRequestResolver());
  }

  function & _registerRootObject()
  {
    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();

    $uow->start();

    $entity = new TestContentServiceNode();

    $node =& $entity->getNodePart();
    $node->set('identifier', 'services');

    $service =& $entity->getServicePart();
    $service->set('title', 'Services page');
    $service->set('name', 'ServiceNode');

    $content =& $entity->getContentPart();
    $content->set('content', 'Some content');
    $content->set('annotation', 'Some annotation');
    $content->set('news_date', '2005-02-11');

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
    $this->db->delete('test_one_table_object');
  }

  function testPerformEntityNotFound()
  {
    $entity =& $this->_registerRootObject();

    $this->assertEqual($this->command->perform(), LIMB_STATUS_OK);

    $service_node =& $this->command->getEntity();
    $this->assertNotEqual($service_node, $entity);
  }

  function testPerformFormDisplayed()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath('/services');

    $this->assertEqual($this->command->perform(), LIMB_STATUS_OK);

    $service_node =& $this->command->getEntity();
    $this->assertIsA($service_node, 'TestContentServiceNode');
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

    $node =& $entity->getNodePart();
    $request->set('parent_node_id', $node->get('id'));
    $request->set('submitted', 1);
    $request->set('title', $title = 'Other title');
    $request->set('identifier', $identifier = 'other_identifier');
    $request->set('annotation', $annotation = 'other annotation');
    $request->set('content', $new_content = 'other content');

    $this->assertEqual($this->command->perform(), LIMB_STATUS_OK);

    $service_node =& $this->command->getEntity();
    $node =& $service_node->getNodePart();
    $service =& $service_node->getServicePart();
    $content =& $service_node->getContentPart();

    $this->assertEqual($service->get('title'), $title);
    $this->assertEqual($node->get('identifier'), $identifier);
    $this->assertEqual($content->get('annotation'), $annotation);
    $this->assertEqual($content->get('content'), $new_content);
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