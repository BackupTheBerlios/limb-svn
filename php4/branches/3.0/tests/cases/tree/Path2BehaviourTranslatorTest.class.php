<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: Path2IdTranslatorTest.class.php 1159 2005-03-14 10:10:35Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/tree/Path2ServiceTranslator.class.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');

Mock :: generatePartial('Path2ServiceTranslator',
                        'Path2ServiceTranslatorSpecialVersion',
                        array('_getPath2IdTranslator'));

Mock :: generate('Path2IdTranslator');

class Path2ServiceTranslatorTest extends LimbTestCase
{
  var $translator;
  var $db;
  var $tree;
  var $id_translator;

  function Path2ServiceTranslatorTest()
  {
    parent :: LimbTestCase('path to service translator tests');
  }

  function setUp()
  {
    $this->id_translator = new MockPath2IdTranslator($this);

    $this->translator = new Path2ServiceTranslatorSpecialVersion($this);
    $this->translator->setReturnReference('_getPath2IdTranslator', $this->id_translator);

    $toolkit = Limb :: toolkit();
    $this->tree = $toolkit->getTree();

    $this->db = new SimpleDb($toolkit->getDbConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();

    $this->id_translator->tally();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_service');
    $this->db->delete('sys_service');
  }

  function testNotFound()
  {
    $this->id_translator->expectOnce('toId', array($path = '/root/no_service'));
    $this->id_translator->setReturnValue('toId', null);

    $this->assertNull($this->translator->toService($path));
  }

  function testToServiceOk()
  {
    $this->db->insert('sys_service', array('oid' => $oid = 10,
                                           'service_id' => $service_id = 50));

    $this->db->insert('sys_service', array('id' => $service_id,
                                           'name' => $service_name = 'test'));

    $this->id_translator->expectOnce('toId', array($path = '/root/test'));
    $this->id_translator->setReturnValue('toId', $oid);
    $service = $this->translator->toService($path);
    $this->assertEqual($service->getName(), $service_name);
  }

  function testToServiceNotFound()
  {
    $this->db->insert('sys_service', array('oid' => $oid = 10,
                                           'service_id' => $service_id = 50));

    $this->id_translator->expectOnce('toId', array($path = '/root/test'));
    $this->id_translator->setReturnValue('toId', $oid);
    $this->assertNull($this->translator->toService($path));
  }
}

?>
