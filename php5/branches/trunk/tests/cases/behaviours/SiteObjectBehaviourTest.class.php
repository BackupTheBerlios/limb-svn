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
require_once(LIMB_DIR . '/class/core/behaviours/SiteObjectBehaviour.class.php');
require_once(LIMB_DIR . '/class/core/tree/TreeDecorator.class.php');
require_once(LIMB_DIR . '/class/core/LimbToolkit.interface.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');

Mock :: generate('LimbToolkit');
Mock :: generate('TreeDecorator');
Mock :: generate('SiteObjectBehaviour');

class SiteObjectBehaviourTestVersion extends SiteObjectBehaviour
{
  function _defineProperties()
  {
    return array(
      'sort_order' => 3,
      'can_be_parent' => 1,
      'icon' => '/shared/images/folder.gif',
    );
  }

  public function defineAction1($state_machine){}
  public function defineAction2($state_machine){}
}

class SiteObjectBehaviourTest extends LimbTestCase
{
  var $db;
  var $object;
  var $behaviour;
  var $toolkit;

  function setUp()
  {
    $this->db = DbFactory :: instance();
    $this->toolkit = new MockLimbToolkit($this);
    $this->toolkit->setReturnValue('getDB', $this->db);

    $this->_cleanUp();

    $this->behaviour = new SiteObjectBehaviourTestVersion();
  }

  function tearDown()
  {
    $this->_cleanUp();

    $this->toolkit->tally();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('sys_behaviour');
    $this->db->sqlDelete('sys_site_object');
    $this->db->sqlDelete('sys_site_object_tree');
  }

  function testGetActionsList()
  {
    $this->assertEqual(
                       array('action1', 'action2'),
                       $this->behaviour->getActionsList());
  }

  function testActionExists()
  {
    $this->assertTrue($this->behaviour->actionExists('action1'));
    $this->assertFalse($this->behaviour->actionExists('no_such_action'));
  }

  function testGetProperty()
  {
    $this->assertIdentical($this->behaviour->getProperty('no_such_property', false), false);

    $this->assertEqual($this->behaviour->getProperty('sort_order'), 3);
  }

  function testGetBehaviourId()
  {
    // test auto create new record
    $id = $this->behaviour->getId();

    $this->db->sqlSelect('sys_behaviour', '*', 'name="site_object_behaviour_test_version"');
    $arr = $this->db->fetchRow();

    $this->assertNotNull($id);

    $this->assertEqual($id, $arr['id']);
    $this->assertEqual($this->behaviour->getProperty('icon'), $arr['icon']);
    $this->assertEqual($this->behaviour->getProperty('can_be_parent'), $arr['can_be_parent']);
    $this->assertEqual($this->behaviour->getProperty('sort_order'), $arr['sort_order']);

    // test only one record for one name
    $id = $this->behaviour->getId();
    $this->db->sqlSelect('sys_behaviour', '*');
    $arr = $this->db->getArray();

    $this->assertEqual(sizeof($arr), 1);
  }

  function testCanBeParent()
  {
    $this->assertTrue($this->behaviour->canBeParent());
  }

  function testCanAcceptChildrenFalse()
  {
    $tree = new MockTreeDecorator($this);
    $tree->expectOnce('canAddNode', array(10));
    $tree->setReturnValue('canAddNode', false);

    $this->toolkit->setReturnValue('getTree', $tree);

    Limb :: registerToolkit($this->toolkit);

    $this->assertFalse(SiteObjectBehaviour :: canAcceptChildren(10));

    $tree->tally();

    Limb :: popToolkit();
  }

  function testCanAcceptChildrenTrue()
  {
    $this->db->sqlInsert('sys_behaviour', array('id' => $behaviour_id = 100,
                                                 'name' => 'test_behaviour'));
    $this->db->sqlInsert('sys_behaviour', array('id' => 1000,
                                                 'name' => 'junk_behaviour'));


    $this->db->sqlInsert('sys_site_object_tree', array('id' => $node_id = 10,
                                                        'root_id' => 1,
                                                        'identifier' => 'test_object',
                                                        'object_id' => $object_id = 20));

    $this->db->sqlInsert('sys_site_object_tree', array('id' => 1000,
                                                        'root_id' => 1,
                                                        'identifier' => 'junk_object',
                                                        'object_id' => 200));

    $this->db->sqlInsert('sys_site_object', array('id' => $object_id,
                                                   'class_id' => 1000,
                                                   'behaviour_id' => $behaviour_id,
                                                   'identifier' => 'test_object'));

    $tree = new MockTreeDecorator($this);
    $tree->expectOnce('canAddNode', array($node_id));
    $tree->setReturnValue('canAddNode', true);

    $mock_behaviour = new MockSiteObjectBehaviour($this);
    $mock_behaviour->expectOnce('canBeParent');
    $mock_behaviour->setReturnValue('canBeParent', true);

    $this->toolkit->setReturnValue('getTree', $tree);
    $this->toolkit->expectOnce('createBehaviour', array('testBehaviour'));
    $this->toolkit->setReturnValue('createBehaviour', $mock_behaviour);

    Limb :: registerToolkit($this->toolkit);

    $this->assertTrue(SiteObjectBehaviour :: canAcceptChildren($node_id));

    $mock_behaviour->tally();
    $tree->tally();

    Limb :: popToolkit();
  }

  function testGetIdsByNames()
  {
    $this->db->sqlInsert('sys_behaviour', array('id' => 10, 'name' => 'test1'));
    $this->db->sqlInsert('sys_behaviour', array('id' => 11, 'name' => 'test2'));
    $this->db->sqlInsert('sys_behaviour', array('id' => 12, 'name' => 'test3'));

    $ids = SiteObjectBehaviour :: getIdsByNames(array('test1', 'test2'));

    sort($ids);
    $this->assertEqual(sizeof($ids), 2);
    $this->assertEqual($ids, array(10, 11));
  }

  function testGetBehaviourNameByIdOk()
  {
    // test this two methods
    $this->assertTrue(false);

    $this->db->sqlInsert('sys_behaviour', array('id' => $behaviour_id = 100,
                                                 'name' => $name = 'test_behaviour'));

    $this->db->sqlInsert('sys_site_object', array('id' => $object_id = 100,
                                                 'behaviour_id' => $behaviour_id));

    $this->assertEqual(SiteObjectBehaviour :: findBehaviourNameById($object_id), $name);
  }

  function testGetBehaviourNameByIdFailed()
  {
    try
    {
      SiteObjectBehaviour :: findBehaviourNameById(100);
      $this->assertTrue(false);
    }
    catch(LimbException $e)
    {
      $this->assertTrue(true);
    }
  }
}

?>