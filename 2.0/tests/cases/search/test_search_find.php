<?php

require_once(TEST_CASES_DIR . 'test_db_case.php');
require_once(LIMB_DIR . '/core/model/search/search.class.php');

class test_search_find extends test_db_case
{
	var $search = null;
	var $dump_file = 'search.sql'; 

	function test_search_find($name = 'search find test case')
	{
		parent :: test_db_case($name);
		
		$this->search = new search();
	} 
		
	function test_simple_find()
	{
		$this->search->set_search_query("губернатор, 
					который 
			всем '<b>СЕРДЕЧНО</b>' говорит привет");
		
		$total = $this->search->find();
		$result = $this->search->get_search_result();
		
		$this->assertEqual($total, 3);
		$this->assertNotEqual($result, array());
				
		$this->assertEqual($result[193]['total'], 3);
		$this->assertEqual($result[193]['results']['content'], 3);
		$this->assertEqual($result[195]['total'], 1);
		$this->assertEqual($result[195]['results']['text'], 1);
		$this->assertEqual($result[196]['total'], 2);
		$this->assertEqual($result[196]['results']['text'], 2);
	}

	function test_certain_attribute_find()
	{
		$params = array('search_attribute_name' => 'text');
		 
		$this->search->set_search_query("губернатор, 
					который 
			всем '<b>СЕРДЕЧНО</b>' говорит привет");
			
		$this->search->set_search_params($params);
			
		$total = $this->search->find();
		
		$result = $this->search->get_search_result();
		
		$this->assertEqual($total, 2);
				
		$this->assertEqual($result[195]['total'], 1);
		$this->assertEqual($result[195]['results']['text'], 1);
		$this->assertEqual($result[196]['total'], 2);
		$this->assertEqual($result[196]['results']['text'], 2);
	}

	function test_only_type_find()
	{
		$params = array('search_class_id' => 28);

		$this->search->set_search_query("губернатор, 
					который 
			всем '<b>СЕРДЕЧНО</b>' говорит привет");
			
		$this->search->set_search_params($params);
		 
		$total = $this->search->find();
		
		$result = $this->search->get_search_result();
		$this->assertEqual($total, 1);
				
		$this->assertEqual($result[196]['total'], 2);
		$this->assertEqual($result[196]['results']['text'], 2);
	}
} 
?>