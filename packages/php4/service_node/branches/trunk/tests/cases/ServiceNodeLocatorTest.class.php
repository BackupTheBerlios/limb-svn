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
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNodeLocator.class.php');
require_once(LIMB_DIR . '/core/UnitOfWork.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');
require_once(LIMB_DIR . '/core/entity/Entity.class.php');
require_once(LIMB_DIR . '/core/ServiceLocation.class.php');
require_once(LIMB_DIR . '/core/NodeConnection.class.php');

Mock :: generate('UnitOfWork');

Mock :: generatePartial('LimbBaseToolkit',
                        'ToolkitServiceNodeLocatorTestVersion',
                        array('getUOW'));

class ServiceNodeLocatorTest extends LimbTestCase
{
  var $db;

  function ServiceNodeLocatorTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDbConnection();
    $this->db = new SimpleDb($conn);

    $this->uow =& new MockUnitOfWork($this);
    $this->toolkit =& new ToolkitServiceNodeLocatorTestVersion($this);
    $this->toolkit->setReturnReference('getUOW', $this->uow);

    $this->_cleanUp();

    Limb :: registerToolkit($this->toolkit);
  }

  function tearDown()
  {
    $this->uow->tally();

    $this->_cleanUp();

    Limb :: restoreToolkit();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_class');
    $this->db->delete('sys_object');
  }

  function testGetCurrentServiceNodeFailedCantGetOID()
  {
    $locator = new ServiceNodeLocator();

    $this->assertNull($locator->getCurrentServiceNode());
  }

  function testGetCurrentServiceCantFindClassName()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $id = 10);

    $locator = new ServiceNodeLocator();
    $this->assertNull($locator->getCurrentServiceNode());
  }

  function testGetCurrentServiceFailedEntityDoesntHasServicePart()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $id = 10);

    $this->db->insert('sys_object', array('oid' => $id = 10,
                                          'class_id' => $class_id = 100));

    $this->db->insert('sys_class', array('id' => $class_id,
                                         'name' => $class_name = 'Test Class name'));

    $entity = new Entity();

    $this->uow->expectOnce('load', array($class_name, $id));
    $this->uow->setReturnReference('load', $entity, array($class_name, $id));

    $locator = new ServiceNodeLocator();
    $this->assertNull($locator->getCurrentServiceNode());
  }

  function testGetCurrentServiceFailedEntityDoesntHasNodePart()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $id = 10);

    $this->db->insert('sys_object', array('oid' => $id = 10,
                                          'class_id' => $class_id = 100));

    $this->db->insert('sys_class', array('id' => $class_id,
                                         'name' => $class_name = 'Test Class name'));

    $entity = new Entity();
    $entity->registerPart('service', new ServiceLocation());

    $this->uow->expectOnce('load', array($class_name, $id));
    $this->uow->setReturnReference('load', $entity, array($class_name, $id));

    $locator = new ServiceNodeLocator();
    $this->assertNull($locator->getCurrentServiceNode());
  }

  function testGetCurrentServiceOk()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $id = 10);

    $this->db->insert('sys_object', array('oid' => $id = 10,
                                          'class_id' => $class_id = 100));

    $this->db->insert('sys_class', array('id' => $class_id,
                                         'name' => $class_name = 'Test Class name'));

    $entity = new Entity();
    $entity->registerPart('service', new ServiceLocation());
    $entity->registerPart('node', new NodeConnection());

    $this->uow->expectOnce('load', array($class_name, $id));
    $this->uow->setReturnReference('load', $entity, array($class_name, $id));

    $locator = new ServiceNodeLocator();
    $this->assertEqual($locator->getCurrentServiceNode(), $entity);
  }

  function testGetCurrentServiceCached()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $id = 10);

    $this->db->insert('sys_object', array('oid' => $id = 10,
                                          'class_id' => $class_id = 100));

    $this->db->insert('sys_class', array('id' => $class_id,
                                         'name' => $class_name = 'Test Class name'));

    $entity = new Entity();
    $entity->registerPart('service', new ServiceLocation());
    $entity->registerPart('node', new NodeConnection());

    $this->uow->expectOnce('load', array($class_name, $id));
    $this->uow->setReturnReference('load', $entity, array($class_name, $id));

    $locator = new ServiceNodeLocator();

    $entity1 =& $locator->getCurrentServiceNode();
    $entity2 =& $locator->getCurrentServiceNode();
    $this->assertEqual($entity1, $entity2);
  }
}

?>