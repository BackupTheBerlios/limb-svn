<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/core/links_manager.class.php');

class links_manager_test extends LimbTestCase 
{
  var $links_manager = null;
  var $db = null;
	  
  function setUp()
  {
  	$this->db =& db_factory :: instance();
   	$this->links_manager = new links_manager();
   	
   	$this->_clean_up();
  }
  
  function tearDown()
  {
  	$this->_clean_up();
  }
  
  function _clean_up()
  {
    $this->db->sql_delete('sys_node_link_group');
    $this->db->sql_delete('sys_node_link');
  }
  
  function test_create_links_group()
  {
    $group_id = $this->links_manager->create_links_group('articles', 'Linked articles');
    
    $this->db->sql_select('sys_node_link_group');
    $arr = $this->db->get_array();
    
    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);
    
    $this->assertEqual($group_id, $record['id']);
    $this->assertEqual('articles', $record['identifier']);
    $this->assertEqual('Linked articles', $record['title']);
  }
  
  function test_double_links_group_creation()
  {
    $group_id = $this->links_manager->create_links_group('articles', 'Linked articles');
    $this->assertIdentical($this->links_manager->create_links_group('articles', 'Linked articles2'), false);
    
    $this->db->sql_select('sys_node_link_group');
    $arr = $this->db->get_array();
    
    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($group_id, $record['id']);
    $this->assertEqual('articles', $record['identifier']);
    $this->assertEqual('Linked articles', $record['title']);
  }
    
  function test_update_links_group()
  {
    $group_id = $this->links_manager->create_links_group('articles', 'Linked articles');
    $this->links_manager->update_links_group($group_id, 'articles2', 'Linked articles2');

    $this->db->sql_select('sys_node_link_group');
    $record = $this->db->fetch_row();

    $this->assertEqual('articles2', $record['identifier']);
    $this->assertEqual('Linked articles2', $record['title']);
  }

  function test_delete_links_group()
  {
    $group_id = $this->links_manager->create_links_group('articles', 'Linked articles');
    
    $this->links_manager->delete_links_group($group_id);

    $this->db->sql_select('sys_node_link_group');
    $arr = $this->db->get_array();
    $this->assertEqual(sizeof($arr), 0);
  }

  function test_set_groups_priority()
  {
    $group_id1 = $this->links_manager->create_links_group('articles1', 'Linked articles1');
    $group_id2 = $this->links_manager->create_links_group('articles2', 'Linked articles2');
    $group_id3 = $this->links_manager->create_links_group('articles3', 'Linked articles3');
    
    $priority_info = array(
      $group_id1 => 3,
      $group_id2 => 2,
      $group_id3 => 1,
    );

    $this->links_manager->set_groups_priority($priority_info);

    $this->db->sql_select('sys_node_link_group', 'sys_node_link_group.id as id', '', 'priority DESC');
    $arr = $this->db->get_array();
    $this->assertEqual(sizeof($arr), 3);

    $record = reset($arr);
    $this->assertEqual($record['id'], $group_id1);

    $record = next($arr);
    $this->assertEqual($record['id'], $group_id2);

    $record = next($arr);
    $this->assertEqual($record['id'], $group_id3);
  }
  
  function test_fetch_group()
  {
    $group_id1 = $this->links_manager->create_links_group('articles1', 'Linked articles1');
    $group_id2 = $this->links_manager->create_links_group('articles2', 'Linked articles2');
    
    $group = $this->links_manager->fetch_group($group_id2);
    
    $this->assertEqual($group['identifier'], 'articles2');
    $this->assertEqual($group['title'], 'Linked articles2');
    $this->assertEqual($group['id'], $group_id2);
  }

  function test_fetch_group_by_identifier_failed()
  {
    $this->assertFalse($this->links_manager->fetch_group_by_identifier('no_such_article'));
  } 
  
  function test_fetch_group_by_identifier()
  {
    $group_id1 = $this->links_manager->create_links_group('articles1', 'Linked articles1');
    $group_id2 = $this->links_manager->create_links_group('articles2', 'Linked articles2');
    
    $group = $this->links_manager->fetch_group_by_identifier('articles2');
    
    $this->assertEqual($group['identifier'], 'articles2');
    $this->assertEqual($group['title'], 'Linked articles2');
    $this->assertEqual($group['id'], $group_id2);
  }  
  
  function test_fetch_groups()
  {
    $group_id1 = $this->links_manager->create_links_group('articles1', 'Linked articles1');
    $group_id2 = $this->links_manager->create_links_group('articles2', 'Linked articles2');
    $group_id3 = $this->links_manager->create_links_group('articles3', 'Linked articles3');
    
    $priority_info = array(
      $group_id1 => 1,
      $group_id2 => 2,
      $group_id3 => 0,
    );

    $this->links_manager->set_groups_priority($priority_info);
    
    $groups = $this->links_manager->fetch_groups();
    
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

  function test_create_link_no_group()
  {
  	$this->assertFalse($this->links_manager->create_link(-10000, 1, 100));
  }
  
  function test_create_link()
  {
    $group_id = $this->links_manager->create_links_group('articles', 'Linked articles');
  	$link_id  = $this->links_manager->create_link($group_id, 1, 100);

    $this->db->sql_select('sys_node_link');
    $arr = $this->db->get_array();
    
    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($link_id, $record['id']);
    $this->assertEqual($group_id, $record['group_id']);
    $this->assertEqual(1, $record['linker_node_id']);
    $this->assertEqual(100, $record['target_node_id']);
  }

  function test_double_create_link()
  {
    $group_id = $this->links_manager->create_links_group('articles', 'Linked articles');

  	$this->links_manager->create_link($group_id, 1, 100);
  	$this->links_manager->create_link($group_id, 1, 100);

    $this->db->sql_select('sys_node_link');
    $arr = $this->db->get_array();
    
    $this->assertEqual(sizeof($arr), 1);
  }

  function test_delete_link()
  {
    $group_id = $this->links_manager->create_links_group('articles', 'Linked articles');
  	$link_id = $this->links_manager->create_link($group_id, 1, 100);

  	$this->links_manager->delete_link($link_id);

    $this->db->sql_select('sys_node_link');
    $arr = $this->db->get_array();
    
    $this->assertEqual(sizeof($arr), 0);
  }
  
  function test_set_links_priority()
  {
    $group_id1 = $this->links_manager->create_links_group('articles', 'Linked articles');

  	$link_id1 = $this->links_manager->create_link($group_id1, 1, 100);
    $link_id2 = $this->links_manager->create_link($group_id1, 2, 101);
  	$link_id3 = $this->links_manager->create_link($group_id1, 3, 102);
  
    $priority_info = array(
      $link_id1 => 3,
      $link_id2 => 2,
      $link_id3 => 1,
    );

    $this->links_manager->set_links_priority($priority_info);

    $this->db->sql_select('sys_node_link', '*', '', 'priority ASC');
    $arr = $this->db->get_array();
    
    $this->assertEqual(sizeof($arr), 3);

    $record = reset($arr);
    $this->assertEqual($record['id'], $link_id3);

    $record = next($arr);
    $this->assertEqual($record['id'], $link_id2);

    $record = next($arr);
    $this->assertEqual($record['id'], $link_id1);
  }

  function test_fetch_target_links_node_ids_for_node()
  {
    $group_id1 = $this->links_manager->create_links_group('articles', 'Linked articles');
    $group_id2 = $this->links_manager->create_links_group('folders', 'Linked folders');

  	$this->links_manager->create_link($group_id1, $linker_node_id = 1, 100);
  	$this->links_manager->create_link($group_id2, $linker_node_id, 200);
  	$this->links_manager->create_link($group_id1, $linker_node_id, 101);

  	$node_ids = $this->links_manager->fetch_target_links_node_ids($linker_node_id, array($group_id1));
  	sort($node_ids);
  	$this->assertEqual($node_ids, array(100, 101));

  	$node_ids = $this->links_manager->fetch_target_links_node_ids($linker_node_id, array($group_id1, $group_id2));
  	sort($node_ids);
  	$this->assertEqual($node_ids, array(100, 101, 200));

  	$node_ids = $this->links_manager->fetch_target_links_node_ids($no_such_linker_node_id = 10, array($group_id1));

  	$this->assertEqual($node_ids, array());
  }

  function test_fetch_target_links_node_ids_for_node_no_group()
  {
    $group_id1 = $this->links_manager->create_links_group('articles', 'Linked articles');
    $group_id2 = $this->links_manager->create_links_group('folders', 'Linked folders');

  	$this->links_manager->create_link($group_id1, $linker_node_id = 1, 100);
  	$this->links_manager->create_link($group_id2, $linker_node_id, 200);
  	$this->links_manager->create_link($group_id1, $linker_node_id, 101);
  	
  	$node_ids = $this->links_manager->fetch_target_links_node_ids($linker_node_id);
  	sort($node_ids);
  	$this->assertEqual($node_ids, array(100, 101, 200));

  	$node_ids = $this->links_manager->fetch_target_links_node_ids($no_such_linker_node_id = 10);

  	$this->assertEqual($node_ids, array());
  }

  function test_fetch_linker_nodes_for_node()
  {
    $group_id1 = $this->links_manager->create_links_group('articles', 'Linked articles');
    $group_id2 = $this->links_manager->create_links_group('folders', 'Linked folders');

  	$this->links_manager->create_link($group_id1, 1, $target_node_id1 = 100);
  	$this->links_manager->create_link($group_id2, 3, $target_node_id1);
  	$this->links_manager->create_link($group_id1, 2, $target_node_id1);

  	$node_ids = $this->links_manager->fetch_back_links_node_ids($target_node_id1, array($group_id1));
  	sort($node_ids);
  	$this->assertEqual($node_ids, array(1, 2));

  	$node_ids = $this->links_manager->fetch_back_links_node_ids($target_node_id1, array($group_id1, $group_id2));
  	sort($node_ids);
  	$this->assertEqual($node_ids, array(1, 2, 3));

  	$node_ids = $this->links_manager->fetch_back_links_node_ids($no_such_target_node_id = 300, array($group_id1));

  	$this->assertEqual($node_ids, array());
  }

  function test_fetch_linker_nodes_for_node_no_group()
  {
    $group_id1 = $this->links_manager->create_links_group('articles', 'Linked articles');
    $group_id2 = $this->links_manager->create_links_group('folders', 'Linked folders');

  	$this->links_manager->create_link($group_id1, 1, $target_node_id1 = 100);
  	$this->links_manager->create_link($group_id2, 3, $target_node_id1);
  	$this->links_manager->create_link($group_id1, 2, $target_node_id1);
    
  	$node_ids = $this->links_manager->fetch_back_links_node_ids($target_node_id1);
  	sort($node_ids);
  	$this->assertEqual($node_ids, array(1, 2, 3));

  	$node_ids = $this->links_manager->fetch_back_links_node_ids($no_such_target_node_id = 300);

  	$this->assertEqual($node_ids, array());
  }
  
}

?>