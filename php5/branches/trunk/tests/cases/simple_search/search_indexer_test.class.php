<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(LIMB_DIR . '/class/core/site_objects/site_object.class.php');
require_once(LIMB_DIR . '/class/search/indexer.class.php');

Mock::generate('site_object');

class search_indexer_test extends LimbTestCase
{
	var $db = null;
	var $site_object = null;
	
	function search_indexer_test($name = 'search indexer test case')
	{
		parent :: LimbTestCase($name);
		
		$this->db =& db_factory :: instance();
	} 
	
	function setUp()
	{
		$this->_clean_up();
		$this->site_object =& new Mocksite_object($this);
		
		$this->site_object->setReturnValue('get_id', 10);
		$this->site_object->setReturnValue('export', 
			array(
				'id' => 10, 
				'title' => "     <b>this</b><p><br>is 
					a      \"TEST\" title    ", 
				'content' => " [this;;] 
							is a content 'test'",
			)
		);
		
		$attributes_definition = array(
				'id' => array('type' => 'numeric'),
				'title' => array('type' => 'string', 'search' => true),
				'content' => array('type' => 'string', 'search' => true),
		);
			
		foreach($attributes_definition as $id => $definition)
			$this->site_object->setReturnValue('get_definition', $definition, array($id));
		
		$this->site_object->setReturnValue('get_class_id', 5);		
	} 
	
	function tearDown()
	{ 
		$this->_clean_up();
		$this->site_object->tally();
	}
	
	function _clean_up()
	{
		$this->db->sql_delete('sys_word');
		$this->db->sql_delete('sys_word_link');
	}
	
	function test_index_object_no_words_in_db()
	{
		$this->site_object->expectAtLeastOnce('get_id');
		$this->site_object->expectAtLeastOnce('get_class_id');
		$this->site_object->expectOnce('export');

		indexer :: add($this->site_object);

		$this->db->sql_select('sys_word');
		$arr = $this->db->get_array();
		
		$this->assertNotEqual($arr, array());
		$this->assertEqual(sizeof($arr), 6);
		
		$this->db->sql_select('sys_word_link');
		$arr = $this->db->get_array();
		
		$this->assertNotEqual($arr, array());
		$this->assertEqual(sizeof($arr), 10);
		
		$sql = "SELECT swl.attribute_name
						FROM sys_word_link swl, sys_word sw
						WHERE swl.word_id=sw.id AND sw.word='this'";
		
		$this->db->sql_exec($sql);
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 2);
		
		$sql = "SELECT swl.*
						FROM sys_word_link swl, sys_word sw
						WHERE swl.word_id=sw.id AND sw.word='content'";
		
		$this->db->sql_exec($sql);
		$row = $this->db->fetch_row();
		$this->assertEqual($row['attribute_name'], 'content');
		$this->assertEqual((int)$row['object_id'], $this->site_object->get_id());
	}
	
	function test_index_2_equal_objects()
	{
		indexer :: add($this->site_object);
		indexer :: add($this->site_object);
		
		$this->db->sql_select('sys_word');
		$arr = $this->db->get_array();
		
		$this->assertNotEqual($arr, array());
		$this->assertEqual(sizeof($arr), 6);
		
		$this->db->sql_select('sys_word_link');
		$arr = $this->db->get_array();
		$this->assertEqual(sizeof($arr), 10);
	}
	
	function test_index_object_with_some_words_in_db()
	{
		$this->db->sql_insert('sys_word', array('word' => 'test', 'object_count' => 1));
		$id1 = $this->db->get_sql_insert_id();
		
		$this->db->sql_insert('sys_word', array('word' => 'content', 'object_count' => 2));
		$id2 = $this->db->get_sql_insert_id();
		
		$this->db->sql_insert('sys_word', array('word' => 'a', 'object_count' => 3));
		$id3 = $this->db->get_sql_insert_id();

		$this->db->sql_insert('sys_word', array('word' => 'yo!', 'object_count' => 1));
		
		indexer :: add($this->site_object);
		
		$this->db->sql_select('sys_word');
		$arr = $this->db->get_array();
		
		$this->assertNotEqual($arr, array());
		$this->assertEqual(sizeof($arr), 7);
		
		$this->db->sql_select('sys_word_link');
		$arr = $this->db->get_array();
		
		$this->db->sql_select('sys_word', '*', array('word' => 'test', 'object_count' => 3));
		$row = $this->db->fetch_row();
		$this->assertNotEqual($row, array());
	}
	
	function test_index_object_with_same_words_in_db()
	{
		$this->db->sql_insert('sys_word', array('word' => 'this', 'object_count' => 1));	
		$this->db->sql_insert('sys_word', array('word' => 'is', 'object_count' => 1));
		$this->db->sql_insert('sys_word', array('word' => 'test', 'object_count' => 1));
		$this->db->sql_insert('sys_word', array('word' => 'title', 'object_count' => 1));		
		$this->db->sql_insert('sys_word', array('word' => 'content', 'object_count' => 1));
		$this->db->sql_insert('sys_word', array('word' => 'a', 'object_count' => 1));

		indexer :: add($this->site_object);
		
		$this->db->sql_select('sys_word', '*', array('word' => 'test', 'object_count' => 3));
		$row = $this->db->fetch_row();
		$this->assertNotEqual($row, array());

	}

} 
?>