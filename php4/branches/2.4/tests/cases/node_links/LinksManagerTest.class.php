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
require_once(LIMB_DIR . '/class/db/LimbDbPool.class.php');
require_once(LIMB_DIR . '/class/LinksManager.class.php');

class LinksManagerTest extends LimbTestCase
{
  var $links_manager = null;
  var $db = null;

  function setUp()
  {
    $this->db =& LimbDbPool :: getConnection();
    $this->links_manager = new LinksManager();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('sys_node_link_group');
    $this->db->sqlDelete('sys_node_link');
  }

  function testCreateLinksGroup()
  {
    $group_id = $this->links_manager->createLinksGroup('articles', 'Linked articles');

    $this->db->sqlSelect('sys_node_link_group');
    $arr = $this->db->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($group_id, $record['id']);
    $this->assertEqual('articles', $record['identifier']);
    $this->assertEqual('Linked articles', $record['title']);
  }

  function testDoubleLinksGroupCreation()
  {
    $group_id = $this->links_manager->createLinksGroup('articles', 'Linked articles');
    $this->assertIdentical($this->links_manager->createLinksGroup('articles', 'Linked articles2'), false);

    $this->db->sqlSelect('sys_node_link_group');
    $arr = $this->db->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($group_id, $record['id']);
    $this->assertEqual('articles', $record['identifier']);
    $this->assertEqual('Linked articles', $record['title']);
  }

  function testUpdateLinksGroup()
  {
    $group_id = $this->links_manager->createLinksGroup('articles', 'Linked articles');
    $this->links_manager->updateLinksGroup($group_id, 'articles2', 'Linked articles2');

    $this->db->sqlSelect('sys_node_link_group');
    $record = $this->db->fetchRow();

    $this->assertEqual('articles2', $record['identifier']);
    $this->assertEqual('Linked articles2', $record['title']);
  }

  function testDeleteLinksGroup()
  {
    $group_id = $this->links_manager->createLinksGroup('articles', 'Linked articles');

    $this->links_manager->deleteLinksGroup($group_id);

    $this->db->sqlSelect('sys_node_link_group');
    $arr = $this->db->getArray();
    $this->assertEqual(sizeof($arr), 0);
  }

  function testSetGroupsPriority()
  {
    $group_id1 = $this->links_manager->createLinksGroup('articles1', 'Linked articles1');
    $group_id2 = $this->links_manager->createLinksGroup('articles2', 'Linked articles2');
    $group_id3 = $this->links_manager->createLinksGroup('articles3', 'Linked articles3');

    $priority_info = array(
      $group_id1 => 3,
      $group_id2 => 2,
      $group_id3 => 1,
    );

    $this->links_manager->setGroupsPriority($priority_info);

    $this->db->sqlSelect('sys_node_link_group', 'sys_node_link_group.id as id', '', 'priority DESC');
    $arr = $this->db->getArray();
    $this->assertEqual(sizeof($arr), 3);

    $record = reset($arr);
    $this->assertEqual($record['id'], $group_id1);

    $record = next($arr);
    $this->assertEqual($record['id'], $group_id2);

    $record = next($arr);
    $this->assertEqual($record['id'], $group_id3);
  }

  function testFetchGroup()
  {
    $group_id1 = $this->links_manager->createLinksGroup('articles1', 'Linked articles1');
    $group_id2 = $this->links_manager->createLinksGroup('articles2', 'Linked articles2');

    $group = $this->links_manager->fetchGroup($group_id2);

    $this->assertEqual($group['identifier'], 'articles2');
    $this->assertEqual($group['title'], 'Linked articles2');
    $this->assertEqual($group['id'], $group_id2);
  }

  function testFetchGroupByIdentifierFailed()
  {
    $this->assertFalse($this->links_manager->fetchGroupByIdentifier('no_such_article'));
  }

  function testFetchGroupByIdentifier()
  {
    $group_id1 = $this->links_manager->createLinksGroup('articles1', 'Linked articles1');
    $group_id2 = $this->links_manager->createLinksGroup('articles2', 'Linked articles2');

    $group = $this->links_manager->fetchGroupByIdentifier('articles2');

    $this->assertEqual($group['identifier'], 'articles2');
    $this->assertEqual($group['title'], 'Linked articles2');
    $this->assertEqual($group['id'], $group_id2);
  }

  function testFetchGroups()
  {
    $group_id1 = $this->links_manager->createLinksGroup('articles1', 'Linked articles1');
    $group_id2 = $this->links_manager->createLinksGroup('articles2', 'Linked articles2');
    $group_id3 = $this->links_manager->createLinksGroup('articles3', 'Linked articles3');

    $priority_info = array(
      $group_id1 => 1,
      $group_id2 => 2,
      $group_id3 => 0,
    );

    $this->links_manager->setGroupsPriority($priority_info);

    $groups = $this->links_manager->fetchGroups();

    $this->assertEqual(sizeof($groups), 3);

    $record = reset($groups);
    $this->assertEqual(key($groups), $group_id3);
    $this->assertEqual($record['identifier'], 'articles3');
    $this->assertEqual($record['title'], 'Linked articles3');
    $this->assertEqual($record['id'], $group_id3);

    $record = next($groups);
    $this->assertEqual(key($groups), $group_id1);
    $this->assertEqual($record['identifier'], 'articles1');
    $this->assertEqual($record['title'], 'Linked articles1');
    $this->assertEqual($record['id'], $group_id1);

    $record = next($groups);
    $this->assertEqual(key($groups), $group_id2);
    $this->assertEqual($record['identifier'], 'articles2');
    $this->assertEqual($record['title'], 'Linked articles2');
    $this->assertEqual($record['id'], $group_id2);

  }

  function testCreateLinkNoGroup()
  {
    $this->assertFalse($this->links_manager->createLink(-10000, 1, 100));
  }

  function testCreateLink()
  {
    $group_id = $this->links_manager->createLinksGroup('articles', 'Linked articles');
    $link_id  = $this->links_manager->createLink($group_id, 1, 100);

    $this->db->sqlSelect('sys_node_link');
    $arr = $this->db->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($link_id, $record['id']);
    $this->assertEqual($group_id, $record['group_id']);
    $this->assertEqual(1, $record['linker_node_id']);
    $this->assertEqual(100, $record['target_node_id']);
  }

  function testDoubleCreateLink()
  {
    $group_id = $this->links_manager->createLinksGroup('articles', 'Linked articles');

    $this->links_manager->createLink($group_id, 1, 100);
    $this->links_manager->createLink($group_id, 1, 100);

    $this->db->sqlSelect('sys_node_link');
    $arr = $this->db->getArray();

    $this->assertEqual(sizeof($arr), 1);
  }

  function testDeleteLink()
  {
    $group_id = $this->links_manager->createLinksGroup('articles', 'Linked articles');
    $link_id = $this->links_manager->createLink($group_id, 1, 100);

    $this->links_manager->deleteLink($link_id);

    $this->db->sqlSelect('sys_node_link');
    $arr = $this->db->getArray();

    $this->assertEqual(sizeof($arr), 0);
  }

  function testSetLinksPriority()
  {
    $group_id1 = $this->links_manager->createLinksGroup('articles', 'Linked articles');

    $link_id1 = $this->links_manager->createLink($group_id1, 1, 100);
    $link_id2 = $this->links_manager->createLink($group_id1, 2, 101);
    $link_id3 = $this->links_manager->createLink($group_id1, 3, 102);

    $priority_info = array(
      $link_id1 => 3,
      $link_id2 => 2,
      $link_id3 => 1,
    );

    $this->links_manager->setLinksPriority($priority_info);

    $this->db->sqlSelect('sys_node_link', '*', '', 'priority ASC');
    $arr = $this->db->getArray();

    $this->assertEqual(sizeof($arr), 3);

    $record = reset($arr);
    $this->assertEqual($record['id'], $link_id3);

    $record = next($arr);
    $this->assertEqual($record['id'], $link_id2);

    $record = next($arr);
    $this->assertEqual($record['id'], $link_id1);
  }

  function testFetchTargetLinksNodeIdsForNode()
  {
    $group_id1 = $this->links_manager->createLinksGroup('articles', 'Linked articles');
    $group_id2 = $this->links_manager->createLinksGroup('folders', 'Linked folders');

    $this->links_manager->createLink($group_id1, $linker_node_id = 1, 100);
    $this->links_manager->createLink($group_id2, $linker_node_id, 200);
    $this->links_manager->createLink($group_id1, $linker_node_id, 101);

    $node_ids = $this->links_manager->fetchTargetLinksNodeIds($linker_node_id, array($group_id1));
    sort($node_ids);
    $this->assertEqual($node_ids, array(100, 101));

    $node_ids = $this->links_manager->fetchTargetLinksNodeIds($linker_node_id, array($group_id1, $group_id2));
    sort($node_ids);
    $this->assertEqual($node_ids, array(100, 101, 200));

    $node_ids = $this->links_manager->fetchTargetLinksNodeIds($no_such_linker_node_id = 10, array($group_id1));

    $this->assertEqual($node_ids, array());
  }

  function testFetchTargetLinksNodeIdsForNodeNoGroup()
  {
    $group_id1 = $this->links_manager->createLinksGroup('articles', 'Linked articles');
    $group_id2 = $this->links_manager->createLinksGroup('folders', 'Linked folders');

    $this->links_manager->createLink($group_id1, $linker_node_id = 1, 100);
    $this->links_manager->createLink($group_id2, $linker_node_id, 200);
    $this->links_manager->createLink($group_id1, $linker_node_id, 101);

    $node_ids = $this->links_manager->fetchTargetLinksNodeIds($linker_node_id);
    sort($node_ids);
    $this->assertEqual($node_ids, array(100, 101, 200));

    $node_ids = $this->links_manager->fetchTargetLinksNodeIds($no_such_linker_node_id = 10);

    $this->assertEqual($node_ids, array());
  }

  function testFetchLinkerNodesForNode()
  {
    $group_id1 = $this->links_manager->createLinksGroup('articles', 'Linked articles');
    $group_id2 = $this->links_manager->createLinksGroup('folders', 'Linked folders');

    $this->links_manager->createLink($group_id1, 1, $target_node_id1 = 100);
    $this->links_manager->createLink($group_id2, 3, $target_node_id1);
    $this->links_manager->createLink($group_id1, 2, $target_node_id1);

    $node_ids = $this->links_manager->fetchBackLinksNodeIds($target_node_id1, array($group_id1));
    sort($node_ids);
    $this->assertEqual($node_ids, array(1, 2));

    $node_ids = $this->links_manager->fetchBackLinksNodeIds($target_node_id1, array($group_id1, $group_id2));
    sort($node_ids);
    $this->assertEqual($node_ids, array(1, 2, 3));

    $node_ids = $this->links_manager->fetchBackLinksNodeIds($no_such_target_node_id = 300, array($group_id1));

    $this->assertEqual($node_ids, array());
  }

  function testFetchLinkerNodesForNodeNoGroup()
  {
    $group_id1 = $this->links_manager->createLinksGroup('articles', 'Linked articles');
    $group_id2 = $this->links_manager->createLinksGroup('folders', 'Linked folders');

    $this->links_manager->createLink($group_id1, 1, $target_node_id1 = 100);
    $this->links_manager->createLink($group_id2, 3, $target_node_id1);
    $this->links_manager->createLink($group_id1, 2, $target_node_id1);

    $node_ids = $this->links_manager->fetchBackLinksNodeIds($target_node_id1);
    sort($node_ids);
    $this->assertEqual($node_ids, array(1, 2, 3));

    $node_ids = $this->links_manager->fetchBackLinksNodeIds($no_such_target_node_id = 300);

    $this->assertEqual($node_ids, array());
  }

}

?>