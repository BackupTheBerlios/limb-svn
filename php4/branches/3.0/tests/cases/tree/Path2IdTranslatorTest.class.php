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
require_once(LIMB_DIR . '/core/tree/Tree.interface.php');
require_once(LIMB_DIR . '/core/LimbBaseToolkit.class.php');
require_once(LIMB_DIR . '/core/db/SimpleDb.class.php');
require_once(LIMB_DIR . '/core/Object.class.php');

Mock :: generatePartial('LimbBaseToolkit',
                 'LimbBaseToolkitPath2IdTranslatorTestVersion',
                 array('getTree'));

Mock :: generate('Tree');

class Path2IdTranslatorTest extends LimbTestCase
{
  var $db;
  var $tree;
  var $toolkit;

  function Path2IdTranslatorTest()
  {
    parent :: LimbTestCase('path to id translator tests');
  }

  function setUp()
  {
    $toolkit = Limb :: toolkit();
    $this->db = new SimpleDb($toolkit->getDbConnection());

    $this->toolkit = new LimbBaseToolkitPath2IdTranslatorTestVersion($this);
    $this->tree = new MockTree($this);
    $this->toolkit->setReturnReference('getTree', $this->tree);

    Limb :: registerToolkit($this->toolkit);

    $this->_cleanUp();
  }

  function tearDown()
  {
    Limb :: restoreToolkit();

    $this->toolkit->tally();
    $this->tree->tally();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('sys_object_to_node');
    $this->db->delete('sys_tree');
  }

  function testToIdNotFound()
  {
    $translator = new Path2IdTranslator();
    $this->assertNull($translator->toId('/root/test1'));
  }

  function testToId()
  {
    $root_node = array('identifier' => 'root');

    $node = array('identifier' => 'test1');

    $this->tree->expectOnce('getNodeByPath', array($path = '/root/test1'));
    $this->tree->setReturnValue('getNodeByPath', array('id' => $node_id = 10));

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id));

    $translator = new Path2IdTranslator();
    $this->assertEqual($id, $translator->toId($path));
  }

  function testToIdUsingExternalOffset()
  {
    $this->tree->expectOnce('getNodeByPath', array($path = '/root/test1'));
    $this->tree->setReturnValue('getNodeByPath', array('id' => $node_id = 10), array($path));

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id));

    $translator = new Path2IdTranslator();
    $translator->setExternalOffset('/limb/cmf');
    $this->assertEqual($id, $translator->toId('/limb/cmf/root/test1'));
  }

  function testToIdUsingExternalOffsetTrimRightSlash()
  {
    $this->tree->expectOnce('getNodeByPath', array($path = '/root/test1'));
    $this->tree->setReturnValue('getNodeByPath', array('id' => $node_id = 10), array($path));

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id));

    $translator = new Path2IdTranslator();
    $translator->setExternalOffset('/limb/cmf/');
    $this->assertEqual($id, $translator->toId('/limb/cmf/root/test1'));
  }

  function testToIdUsingInternalOffset()
  {
    $this->tree->expectOnce('getNodeByPath', array($path = '/root/test1'));
    $this->tree->setReturnValue('getNodeByPath', array('id' => $node_id = 10), array($path));

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id));

    $translator = new Path2IdTranslator();
    $translator->setInternalOffset('/root');
    $this->assertEqual($id, $translator->toId('/test1'));
  }

  function testToIdUsingBothOffsets()
  {
    $this->tree->expectOnce('getNodeByPath', array($path = '/root/test1'));
    $this->tree->setReturnValue('getNodeByPath', array('id' => $node_id = 10), array($path));

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id));

    $translator = new Path2IdTranslator();
    $translator->setInternalOffset('/root');
    $translator->setExternalOffset('/limb/cmf/');
    $this->assertEqual($id, $translator->toId('/limb/cmf/test1'));
  }

  function testToPathNotFound()
  {
    $translator = new Path2IdTranslator();
    $this->assertNull($translator->toPath(1000));
  }

  function testToPath()
  {
    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id = 10));

    $this->tree->expectOnce('getPathToNode', array($node_id));
    $this->tree->setReturnValue('getPathToNode', $path = '/root/test1');

    $translator = new Path2IdTranslator();
    $this->assertEqual('/root/test1', $translator->toPath($id));
  }

  function testToPathUsingExternalOffset()
  {
    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id = 10));

    $this->tree->expectOnce('getPathToNode', array($node_id));
    $this->tree->setReturnValue('getPathToNode', $path = '/root/test1');

    $translator = new Path2IdTranslator();
    $translator->setExternalOffset('/limb/cmf');
    $this->assertEqual('/limb/cmf/root/test1', $translator->toPath($id));
  }

  function testToPathUsingExternalOffsetTrimSlash()
  {
    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id = 10));

    $this->tree->expectOnce('getPathToNode', array($node_id));
    $this->tree->setReturnValue('getPathToNode', $path = '/root/test1');

    $translator = new Path2IdTranslator();
    $translator->setExternalOffset('/limb/cmf/');
    $this->assertEqual('/limb/cmf/root/test1', $translator->toPath($id));
  }

  function testToPathUsingInternalOffset()
  {
    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id = 10));

    $this->tree->expectOnce('getPathToNode', array($node_id));
    $this->tree->setReturnValue('getPathToNode', $path = '/root/test1');

    $translator = new Path2IdTranslator();
    $translator->setInternalOffset('/root/');
    $this->assertEqual('/test1', $translator->toPath($id));
  }

  function testToPathUsingBothOffsets()
  {
    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $node_id = 10));

    $this->tree->expectOnce('getPathToNode', array($node_id));
    $this->tree->setReturnValue('getPathToNode', $path = '/root/test1');

    $translator = new Path2IdTranslator();

    $translator->setExternalOffset('/limb/cmf/');
    $translator->setInternalOffset('/root');
    $this->assertEqual('/limb/cmf/test1', $translator->toPath($id));
  }

  function testGetPathToObjectByParentNodeId()
  {
    $object = new Object();
    $object->set('parent_node_id', $parent_node_id = 10);
    $object->set('identifier', $identifier = 'test_item');

    $this->tree->expectOnce('getPathToNode', array($parent_node_id));
    $this->tree->setReturnValue('getPathToNode', $path = '/internal/whatever', array($parent_node_id));

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $parent_node_id));

    $path2id_translator = new Path2IdTranslator();
    $path2id_translator->setExternalOffset('/external/');
    $path2id_translator->setInternalOffset('/internal/');

    $this->assertEqual($path2id_translator->getPathToObject($object), '/external/whatever/test_item');
  }

  function testGetPathToObjectByParentNodeIdCached()
  {
    $object = new Object();
    $object->set('parent_node_id', $parent_node_id = 10);
    $object->set('identifier', $identifier = 'test_item');

    $this->tree->expectOnce('getPathToNode', array($parent_node_id));
    $this->tree->setReturnValue('getPathToNode', $path = 'whatever', array($parent_node_id));

    $this->db->insert('sys_object_to_node', array('oid' => $id = 200, 'node_id' => $parent_node_id));

    $path2id_translator = new Path2IdTranslator();

    $path1 = $path2id_translator->getPathToObject($object);
    $path2 = $path2id_translator->getPathToObject($object);

    $this->assertEqual($path1, $path2);
  }

  function testGetPathToObjectByNodeID()
  {
    $object = new Object();
    $object->set('node_id', $node_id = 10);
    $object->set('identifier', $identifier = 'test_item');

    $this->tree->expectOnce('getPathToNode', array($node_id));
    $this->tree->setReturnValue('getPathToNode', $path = '/internal/whatever', array($node_id));

    $path2id_translator = new Path2IdTranslator();

    $path2id_translator->setExternalOffset('/external/');
    $path2id_translator->setInternalOffset('/internal/');

    $this->assertEqual($path2id_translator->getPathToObject($object), '/external/whatever');
  }

  function testGetPathToObjectByNodeIDCached()
  {
    $object = new Object();
    $object->set('node_id', $node_id = 10);
    $object->set('identifier', $identifier = 'test_item');

    $this->tree->expectOnce('getPathToNode', array($node_id));
    $this->tree->setReturnValue('getPathToNode', $path = 'whatever', array($node_id));

    $path2id_translator = new Path2IdTranslator();

    $path1 = $path2id_translator->getPathToObject($object);
    $path2 = $path2id_translator->getPathToObject($object);
    $this->assertEqual($path1, $path2);
  }
}

?>
