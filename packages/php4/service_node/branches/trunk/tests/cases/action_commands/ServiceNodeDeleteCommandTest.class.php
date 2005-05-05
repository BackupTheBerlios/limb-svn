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
require_once(LIMB_SERVICE_NODE_DIR . '/action_commands/ServiceNodeDeleteCommand.class.php');
require_once(LIMB_DIR . '/tests/cases/orm/data_mappers/OneTableObjectMapperTestDbTable.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/action_commands/TestContentServiceNode.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/tests/cases/action_commands/TestContentServiceNodeMapper.class.php');
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(LIMB_DIR . '/core/request_resolvers/TreeBasedEntityRequestResolver.class.php');

class ServiceNodeDeleteCommandTest extends LimbTestCase
{
  var $db;
  var $command;

  function ServiceNodeDeleteCommandTest()
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
    $toolkit->setRequestResolver('tree_based_entity', new TreeBasedEntityRequestResolver());
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

  function & _registerRootObject()
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

  function testPerformDelete()
  {
    $entity =& $this->_registerRootObject();
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath('/services');

    $command = new ServiceNodeDeleteCommand();
    $this->assertEqual($command->perform(), LIMB_STATUS_OK);

    $response =& $toolkit->getResponse();
    $this->assertTrue($response->isRedirected());

    $uow =& $toolkit->getUOW();
    $this->assertTrue($uow->isDeleted($entity));
  }
}

?>