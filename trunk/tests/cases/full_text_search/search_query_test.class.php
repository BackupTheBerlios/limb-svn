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
require_once(LIMB_DIR . '/core/model/search/search_query.class.php');

class search_query_test extends LimbTestCase
{
	var $query_object = null;

	function search_query_test($name = 'search query test case')
	{
		parent :: LimbTestCase($name);
	} 
	
	function setUp()
	{
		$this->query_object = new search_query();
	}
	
	function test_is_empty()
	{	
		$this->assertTrue($this->query_object->is_empty());
	}
		
	function test_add()
	{	
		$this->query_object->add('wow');
		$this->query_object->add('yo');
		
		$this->assertEqual($this->query_object->to_string(), 'wow yo');	
	}	
} 
?>