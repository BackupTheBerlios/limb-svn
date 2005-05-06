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
require_once(LIMB_SERVICE_NODE_DIR . '/request_resolvers/ServiceNodeRequestResolver.class.php');
require_once(LIMB_SERVICE_NODE_DIR . '/ServiceNode.class.php');
require_once(LIMB_DIR . '/core/UnitOfWork.class.php');
require_once(WACT_ROOT . '/datasource/dataspace.inc.php');

Mock :: generate('UnitOfWork');

Mock :: generatePartial('LimbBaseToolkit',
                        'ToolkitServiceNodeRequestResolverTestVersion',
                        array('getUOW'));

class ServiceNodeRequestResolverTest extends LimbTestCase
{
  var $db;

  function ServiceNodeRequestResolverTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDbConnection();
    $this->db = new SimpleDb($conn);

    $this->uow =& new MockUnitOfWork($this);
    $this->toolkit =& new ToolkitServiceNodeRequestResolverTestVersion($this);
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

  function testResolveFailedCantGetOID()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $resolver = new ServiceNodeRequestResolver();

    $this->assertNull($resolver->resolve($request));
  }

  function testResolverFailedCantFindClassName()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $id = 10);

    $resolver = new ServiceNodeRequestResolver();
    $this->assertNull($resolver->resolve($request));
  }

  function testResolveOk()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $id = 10);

    $this->db->insert('sys_object', array('oid' => $id = 10,
                                          'class_id' => $class_id = 100));

    $this->db->insert('sys_class', array('id' => $class_id,
                                         'name' => $class_name = 'Test Class name'));

    $entity = new ServiceNode();

    $this->uow->expectOnce('load', array($class_name, $id));
    $this->uow->setReturnReference('load', $entity, array($class_name, $id));

    $resolver = new ServiceNodeRequestResolver();
    $this->assertEqual($resolver->resolve($request), $entity);
  }

  function testResolveCached()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();
    $request->set('id', $id = 10);

    $this->db->insert('sys_object', array('oid' => $id = 10,
                                          'class_id' => $class_id = 100));

    $this->db->insert('sys_class', array('id' => $class_id,
                                         'name' => $class_name = 'Test Class name'));

    $entity = new ServiceNode();

    $this->uow->expectOnce('load', array($class_name, $id));
    $this->uow->setReturnReference('load', $entity, array($class_name, $id));

    $resolver = new ServiceNodeRequestResolver();

    $entity1 =& $resolver->resolve($request);
    $entity2 =& $resolver->resolve($request);
    $this->assertEqual($entity1, $entity2);
  }
}

?>