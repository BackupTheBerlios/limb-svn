<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/request_resolvers/TreeBasedEntityRequestResolver.class.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');
require_once(LIMB_DIR . '/core/UnitOfWork.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'ToolkitTreeBasedEntityRequestResolverTestVersion',
                        array('getUOW',
                              'getPath2IdTranslator'));

Mock :: generate('Path2IdTranslator');
Mock :: generate('UnitOfWork');

class TreeBasedEntityRequestResolverTest extends LimbTestCase
{
  var $path2id_resolver;
  var $toolkit;
  var $db;

  function TreeBasedEntityRequestResolverTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->path2id_resolver = new MockPath2IdTranslator($this);

    $this->toolkit = new ToolkitTreeBasedEntityRequestResolverTestVersion($this);

    $conn =& $this->toolkit->getDbConnection();
    $this->db = new SimpleDb($conn);

    $this->toolkit->setReturnReference('getPath2IdTranslator', $this->path2id_resolver);

    Limb :: registerToolkit($this->toolkit);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->path2id_resolver->tally();

    Limb :: restoreToolkit();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_object');
    $this->db->delete('sys_class');
    $this->db->delete('sys_object_to_service');
    $this->db->delete('sys_service');
  }

  function testGetRequestedEntityNotFound()
  {
    $resolver = new TreeBasedEntityRequestResolver();
    $request =& $this->toolkit->getRequest();

    $this->assertFalse($resolver->resolve($request));
  }

  function testGetRequestedEntityCantMapToId()
  {
    $resolver = new TreeBasedEntityRequestResolver();

    $request =& $this->toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = 'whatever');

    $this->path2id_resolver->expectOnce('toId', array($path));
    $this->path2id_resolver->setReturnValue('toId', null);

    $this->assertFalse($resolver->resolve($request));
  }

  function testGetRequestedEntityCantFind()
  {
    $resolver = new TreeBasedEntityRequestResolver();

    $request =& $this->toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = 'whatever');

    $this->path2id_resolver->expectOnce('toId', array($path));
    $this->path2id_resolver->setReturnValue('toId', $id = 10);

    $this->assertFalse($resolver->resolve($request));
  }

  function testGetRequestedEntityByPathOk()
  {
    $uow = new MockUnitOfWork($this);
    $this->toolkit->setReturnReference('getUOW', $uow);

    $request =& $this->toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = 'whatever');

    $this->path2id_resolver->expectOnce('toId', array($path));
    $this->path2id_resolver->setReturnValue('toId', $id = 10);

    $this->db->insert('sys_object', array('oid' => $id,
                                          'class_id' => $class_id = 100));

    $this->db->insert('sys_class', array('id' => $class_id,
                                         'name' => $class_name = 'TestClass'));

    $uow->expectOnce('load', array($class_name, $id));
    $uow->setReturnValue('load', $expected_entity = 'whatever');

    $resolver = new TreeBasedEntityRequestResolver();
    $this->assertEqual($resolver->resolve($request), $expected_entity);

    $uow->tally();
  }

  function testGetRequestedEntityByOIDOk()
  {
    $uow = new MockUnitOfWork($this);
    $this->toolkit->setReturnReference('getUOW', $uow);

    $request =& $this->toolkit->getRequest();
    $request->set('oid', $id = 10);

    $this->path2id_resolver->expectNever('toId');

    $this->db->insert('sys_object', array('oid' => $id,
                                          'class_id' => $class_id = 100));

    $this->db->insert('sys_class', array('id' => $class_id,
                                         'name' => $class_name = 'TestClass'));

    $uow->expectOnce('load', array($class_name, $id));
    $uow->setReturnValue('load', $expected_entity = 'whatever');

    $resolver = new TreeBasedEntityRequestResolver();
    $this->assertEqual($resolver->resolve($request), $expected_entity);

    $uow->tally();
  }
}

?>
