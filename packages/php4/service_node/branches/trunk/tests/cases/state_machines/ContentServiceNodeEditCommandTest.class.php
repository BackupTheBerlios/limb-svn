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
require_once(LIMB_SERVICE_NODE_DIR . '/state_machines/ContentServiceNodeEditCommand.class.php');
require_once(LIMB_DIR . '/tests/cases/orm/data_mappers/OneTableObjectMapperTestDbTable.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/state_machines/TestContentServiceNode.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/state_machines/TestContentServiceNodeMapper.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/state_machines/EditTestContentServiceNodeValidator.class.php');
require_once(WACT_ROOT . '/template/template.inc.php');

class ContentServiceNodeEditCommandTest extends LimbTestCase
{
  var $db;
  var $command;

  function ContentServiceNodeEditCommandTest()
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
    $entity_handle = new TestContentServiceNode();
    $validator_handle = new EditTestContentServiceNodeValidator();

    RegisterTestingTemplate($form_template_path, '<form id="' . $form_name . '" runat="server"></form>');

    // dataspace field => content part field
    $content_map = array('annotation' => 'annotation',
                         'content' => 'content');

    $this->command = new ContentServiceNodeEditCommand($form_template_path,
                                                         $form_name,
                                                         $entity_handle,
                                                         $validator_handle,
                                                         $content_map);

    Limb :: saveToolkit();
  }

  function _registerRootObject()
  {
    $toolkit =& Limb :: toolkit();
    $uow =& $toolkit->getUOW();

    $uow->start();

    $entity = new TestContentServiceNode();

    $node =& $entity->getPart('node');
    $node->set('identifier', 'services');

    $service =& $entity->getPart('service');
    $service->set('title', 'Services page');
    $service->set('name', 'ServiceNode');

    $content =& $entity->getPart('content');
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

    $this->assertIsA($context->getObject('entity'), 'TestContentServiceNode');

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
    $request->set('annotation', $annotation = 'other annotation');
    $request->set('content', $new_content = 'other content');

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
    $content =& $entity->getPart('content');

    $this->assertEqual($service->get('title'), $title);
    $this->assertEqual($node->get('identifier'), $identifier);
    $this->assertEqual($content->get('annotation'), $annotation);
    $this->assertEqual($content->get('content'), $new_content);
  }
}

?>