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
require_once(LIMB_DIR . '/class/search/full_text_indexer.class.php');

Mock::generate('site_object');

class full_text_search_indexer_test extends LimbTestCase
{
	var $db = null;
	var $site_object = null;
	var $indexer = null;
		
	function setUp()
	{
		$this->db =& db_factory :: instance();
		
		$this->_clean_up();
		
		$this->indexer =& new full_text_indexer();
		
		$this->site_object =& new Mocksite_object($this);
		
		$this->site_object->setReturnValue('get_id', 10);
		$this->site_object->setReturnValue('export', 
			array(
				'id' => 10, 
				'title' => "     <b>this</b><p><br>is 
					a      \"TEST\" title    ", 
				'content' => " [this;;] 
							is a content 'test'",
				'no_search' => 'wow',
				'default_weight_field' => 'this is a field'
			)
		);
		
		$attributes_definition = array(
				'id' => array('type' => 'numeric'),
				'title' => array('type' => 'string', 'search' => true, 'search_weight' => 10),
				'content' => array('type' => 'string', 'search' => true, 'search_weight' => 5),
				'no_search' => array(),
				'default_weight_field' => array('type' => 'string', 'search' => true),
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
		$this->db->sql_delete('sys_full_text_index');
	}
	
	function test_index_object_no_words_in_db()
	{
		$this->site_object->expectAtLeastOnce('get_id');
		$this->site_object->expectAtLeastOnce('get_class_id');
		$this->site_object->expectAtLeastOnce('export');

		$this->indexer->add($this->site_object);
		
		$this->db->sql_select('sys_full_text_index', '*', '', 'id');
		$arr = $this->db->get_array();
		
		$this->assertNotEqual($arr, array());
		$this->assertEqual(sizeof($arr), 3);
		
		$record = reset($arr);
		$this->assertEqual($record['attribute'], 'title');
		$this->assertEqual((int)$record['object_id'], $this->site_object->get_id());
		$this->assertEqual((int)$record['class_id'], $this->site_object->get_class_id());
		$this->assertEqual($record['body'], 'this is a test title');
		$this->assertEqual($record['weight'], 10);

		$record = next($arr);
		$this->assertEqual($record['attribute'], 'content');
		$this->assertEqual((int)$record['object_id'], $this->site_object->get_id());
		$this->assertEqual((int)$record['class_id'], $this->site_object->get_class_id());
		$this->assertEqual($record['body'], 'this is a content test');
		$this->assertEqual($record['weight'], 5);

		$record = next($arr);
		$this->assertEqual($record['attribute'], 'default_weight_field');
		$this->assertEqual((int)$record['object_id'], $this->site_object->get_id());
		$this->assertEqual((int)$record['class_id'], $this->site_object->get_class_id());
		$this->assertEqual($record['body'], 'this is a field');
		$this->assertEqual($record['weight'], 1);
	}
	
	function test_index_2_equal_objects()
	{
		$this->indexer->add($this->site_object);
		$this->indexer->add($this->site_object);
		
		$this->test_index_object_no_words_in_db();
	}

} 
?>