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
require_once(LIMB_SERVICE_NODE_DIR . '/state_machines/CommonServiceNodeDeleteCommand.class.php');
require_once(LIMB_DIR . '/tests/cases/orm/data_mappers/OneTableObjectMapperTestDbTable.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/state_machines/TestContentServiceNode.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/state_machines/TestContentServiceNodeMapper.class.php');
require_once(WACT_ROOT . '/template/template.inc.php');

class CommonServiceNodeDeleteCommandTest extends LimbTestCase
{
  var $db;
  var $command;

  function CommonServiceNodeDeleteCommandTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDBConnection();
    $this->db =& new SimpleDB($conn);

    $this->_cleanUp();

    $this->command = new CommonServiceNodeDeleteCommand();

    Limb :: saveToolkit();
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
    $this->db->delete('test_one_table_object');
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

  function testPerformInitFailed()
  {
    $this->assertEqual($this->command->perform(new DataSpace()), LIMB_STATUS_OK);

    $this->assertEqual($this->command->getStateHistory(), array(
                                                     array('init_object' => LIMB_STATUS_ERROR),
                                                     array('error' => LIMB_STATUS_OK),
                                                     array('render' => LIMB_STATUS_OK),
                                                     ));
  }

  function testPerformDelete()
  {
    $entity =& $this->_registerRootObject();

    $toolkit =& Limb :: toolkit();
    $toolkit->setCurrentEntity($entity);

    $this->assertEqual($this->command->perform(new DataSpace()), LIMB_STATUS_OK);

    $this->assertEqual($this->command->getStateHistory(), array(
                                                     array('init_object' => LIMB_STATUS_OK),
                                                     array('delete' => LIMB_STATUS_OK),
                                                     array('redirect' => LIMB_STATUS_OK),
                                                     ));

    $response =& $toolkit->getResponse();
    $this->assertTrue($response->isRedirected());

    $uow =& $toolkit->getUOW();
    $this->assertTrue($uow->isDeleted($entity));
  }
}

?>