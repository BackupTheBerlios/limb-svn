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
require_once(LIMB_DIR . '/core/tree/Path2BehaviourTranslator.class.php');
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

Mock :: generatePartial('Path2BehaviourTranslator',
                        'Path2BehaviourTranslatorSpecialVersion',
                        array('_getPath2IdTranslator'));

Mock :: generate('Path2IdTranslator');

class Path2BehaviourTranslatorTest extends LimbTestCase
{
  var $translator;
  var $db;
  var $tree;
  var $id_translator;

  function Path2BehaviourTranslatorTest()
  {
    parent :: LimbTestCase('path to behaviour translator tests');
  }

  function setUp()
  {
    $this->id_translator = new MockPath2IdTranslator($this);

    $this->translator = new Path2BehaviourTranslatorSpecialVersion($this);
    $this->translator->setReturnReference('_getPath2IdTranslator', $this->id_translator);

    $this->db = new SimpleDb(LimbDbPool :: getConnection());

    $toolkit = Limb :: toolkit();
    $this->tree = $toolkit->getTree();

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
    $this->db->delete('sys_behaviour');
  }

  function testNotFound()
  {
    $this->id_translator->expectOnce('toId', array($path = '/root/no_behaviour'));
    $this->id_translator->setReturnValue('toId', null);

    $this->assertNull($this->translator->toBehaviour($path));
  }

  function testToBehaviourOk()
  {
    $this->db->insert('sys_service', array('oid' => $oid = 10,
                                           'behaviour_id' => $behaviour_id = 50));

    $this->db->insert('sys_behaviour', array('id' => $behaviour_id,
                                           'name' => $behaviour_name = 'test'));

    $this->id_translator->expectOnce('toId', array($path = '/root/test'));
    $this->id_translator->setReturnValue('toId', $oid);
    $behaviour = $this->translator->toBehaviour($path);
    $this->assertEqual($behaviour->getName(), $behaviour_name);
  }

  function testToBehaviourNotFound()
  {
    $this->db->insert('sys_service', array('oid' => $oid = 10,
                                           'behaviour_id' => $behaviour_id = 50));

    $this->id_translator->expectOnce('toId', array($path = '/root/test'));
    $this->id_translator->setReturnValue('toId', $oid);
    $this->assertNull($this->translator->toBehaviour($path));
  }
}

?>
