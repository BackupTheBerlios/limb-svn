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
require_once(LIMB_DIR . '/core/services/TreeBasedServiceTranslator.class.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/UnitOfWork.class.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');
require_once(LIMB_DIR . '/core/Service.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                        'ToolkitTreeBasedServiceTranslatorTestVersion',
                        array('getUOW',
                              'getPath2IdTranslator'));

Mock :: generate('Path2IdTranslator');
Mock :: generate('UnitOfWork');

class TreeBasedServiceTranslatorTest extends LimbTestCase
{
  var $uow;
  var $path2id_translator;
  var $toolkit;
  var $db;

  function TreeBasedServiceTranslatorTest()
  {
    parent :: LimbTestCase('tree based service translator test');
  }

  function setUp()
  {
    $this->uow = new MockUnitOfWork($this);
    $this->path2id_translator = new MockPath2IdTranslator($this);

    $this->toolkit = new ToolkitTreeBasedServiceTranslatorTestVersion($this);

    $conn =& $this->toolkit->getDbConnection();
    $this->db = new SimpleDb($conn);

    $this->toolkit->setReturnReference('getUOW', $this->uow);
    $this->toolkit->setReturnReference('getPath2IdTranslator', $this->path2id_translator);

    Limb :: registerToolkit($this->toolkit);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->uow->tally();
    $this->path2id_translator->tally();

    Limb :: restoreToolkit();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_class');
    $this->db->delete('sys_object');
  }

  function testGetServiceOk()
  {
    $translator = new TreeBasedServiceTranslator();

    $request =& $this->toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = 'whatever');

    $this->path2id_translator->expectOnce('toId', array($path));
    $this->path2id_translator->setReturnValue('toId', $id = 10);

    $this->db->insert('sys_object', array('oid' => $id,
                                          'class_id' => $class_id = 100));

    $this->db->insert('sys_class', array('id' => $class_id,
                                          'name' => $class_name = 'test class name'));

    $this->uow->expectOnce('load', array($class_name, $id));
    $service = new Service();
    $service->attachService($expected_service = new Object());
    $this->uow->setReturnValue('load', $service);

    $service = $translator->getService($request);

    $this->assertEqual($service, $expected_service);
  }

  function testGetServiceFailedCantMapToId()
  {
    $translator = new TreeBasedServiceTranslator();

    $request =& $this->toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = 'whatever');

    $this->path2id_translator->expectOnce('toId', array($path));
    $this->path2id_translator->setReturnValue('toId', null);

    $this->uow->expectNever('load');

    $this->assertNull($translator->getService($request));
  }

  function testGetServiceFailedCantLoad()
  {
    $translator = new TreeBasedServiceTranslator();

    $request =& $this->toolkit->getRequest();
    $uri =& $request->getUri();
    $uri->setPath($path = 'whatever');

    $this->path2id_translator->expectOnce('toId', array($path));
    $this->path2id_translator->setReturnValue('toId', $id = 10);

    $this->db->insert('sys_object', array('oid' => $id,
                                          'class_id' => $class_id = 100));

    $this->db->insert('sys_class', array('id' => $class_id,
                                          'name' => $class_name = 'test class name'));

    $this->uow->expectOnce('load', array($class_name, $id));
    $this->uow->setReturnValue('load', null);

    $this->assertNull($translator->getService($request));
  }
}

?>
