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
require_once(LIMB_DIR . '/core/tree/Path2IdTranslator.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

class Path2IdTranslatorTest extends LimbTestCase
{
  var $translator;
  var $db;
  var $tree;

  function Path2IdTranslatorTest()
  {
    parent :: LimbTestCase('path to id translator tests');
  }

  function setUp()
  {
    $this->translator = new Path2IdTranslator();
    $this->db = new SimpleDb(LimbDbPool :: getConnection());

    $toolkit = Limb :: toolkit();
    $this->tree = $toolkit->getTree();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_object_to_node');
    $this->db->delete('sys_tree');
  }

  function testToIdNotFound()
  {
    $this->assertNull($this->translator->toId('/root/test1'));
  }

  function testToId()
  {
     $root_node = array('identifier' => 'root');

     $node = array('identifier' => 'test1');

    $root_node_id = $this->tree->createRootNode($root_node);
    $node_id = $this->tree->createSubNode($root_node_id, $node);

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id));

    $this->assertEqual($id, $this->translator->toId('/root/test1'));
  }

  function testToIdUsingExternalOffset()
  {
     $root_node = array('identifier' => 'root');

     $node = array('identifier' => 'test1');

    $root_node_id = $this->tree->createRootNode($root_node);
    $node_id = $this->tree->createSubNode($root_node_id, $node);

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id));

    $this->translator->setExternalOffset('/limb/cmf');
    $this->assertEqual($id, $this->translator->toId('/limb/cmf/root/test1'));

    $this->translator->setExternalOffset('/limb/cmf/');
    $this->assertEqual($id, $this->translator->toId('/limb/cmf/root/test1'));
  }

  function testToIdUsingInternalOffset()
  {
     $root_node = array('identifier' => 'root');

     $node = array('identifier' => 'test1');

    $root_node_id = $this->tree->createRootNode($root_node);
    $node_id = $this->tree->createSubNode($root_node_id, $node);

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id));

    $this->translator->setInternalOffset('/root');
    $this->assertEqual($id, $this->translator->toId('/test1'));

    $this->translator->setExternalOffset('/limb/cmf/');
    $this->assertEqual($id, $this->translator->toId('/limb/cmf/test1'));
  }

  function testToPathNotFound()
  {
    $this->assertNull($this->translator->toPath(1000));
  }

  function testToPath()
  {
     $root_node = array('identifier' => 'root');

     $node = array('identifier' => 'test1');

    $root_node_id = $this->tree->createRootNode($root_node);
    $node_id = $this->tree->createSubNode($root_node_id, $node);

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id));

    $this->assertEqual('/root/test1', $this->translator->toPath($id));
  }

  function testToPathUsingExternalOffset()
  {
    $root_node = array('identifier' => 'root');
    $node = array('identifier' => 'test1');

    $root_node_id = $this->tree->createRootNode($root_node);
    $node_id = $this->tree->createSubNode($root_node_id, $node);

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id));

    $this->translator->setExternalOffset('/limb/cmf');
    $this->assertEqual('/limb/cmf/root/test1', $this->translator->toPath($id));

    $this->translator->setExternalOffset('/limb/cmf/');
    $this->assertEqual('/limb/cmf/root/test1', $this->translator->toPath($id));
  }

  function testToPathUsingInternalOffset()
  {
    $root_node = array('identifier' => 'root');
    $node = array('identifier' => 'test1');

    $root_node_id = $this->tree->createRootNode($root_node);
    $node_id = $this->tree->createSubNode($root_node_id, $node);

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id));

    $this->translator->setExternalOffset('/limb/cmf');
    $this->translator->setInternalOffset('/root/');
    $this->assertEqual('/limb/cmf/test1', $this->translator->toPath($id));

    $this->translator->setExternalOffset('/limb/cmf/');
    $this->translator->setInternalOffset('/root');
    $this->assertEqual('/limb/cmf/test1', $this->translator->toPath($id));

  }

}

?>
