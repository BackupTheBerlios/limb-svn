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
require_once(TEST_CASES_DIR . 'test_db_case.php');
require_once(LIMB_DIR . '/core/model/search/full_text_search.class.php');
require_once(LIMB_DIR . '/core/model/search/search_query.class.php');

class test_search_full_text_find extends test_db_case
{
	var $search = null;
	var $search_query = null;
	var $dump_file = 'full_text_search.sql'; 

	function test_search_full_text_find($name = 'full text search find test case')
	{
		parent :: test_db_case($name);
	}
	
	function setUp()
	{
		parent :: setUp();
		
		$this->search_query = new search_query();
		$this->search = new full_text_search();
	} 
		
	function test_simple_find()
	{	
		$this->search_query->add('mysql');
		$this->search_query->add('root');
		
		$result = $this->search->find($this->search_query);
		
		$this->assertEqual(array_keys($result),
			array(24, 26)
		);
	}

	function test_simple_find_only_class()
	{	
		$this->search_query->add('данных');
		
		$result = $this->search->find($this->search_query, 100);
		$this->assertEqual(array_keys($result),
			array(20)
		);
	}
	
	function test_simple_find_by_ids()
	{	
		$this->search_query->add('mysql');
		$this->search_query->add('root');

		$result = $this->search->find_by_ids(array(24), $this->search_query);
		$this->assertEqual(array_keys($result),
			array(24)
		);
	}

} 
?>