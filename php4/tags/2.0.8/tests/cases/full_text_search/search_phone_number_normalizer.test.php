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
require_once(LIMB_DIR . '/core/model/search/normalizers/search_phone_number_normalizer.class.php');

class test_search_phone_number_normalizer extends UnitTestCase
{
	var $normalizer = null;

	function test_search_phone_number_normalizer($name = 'phone number search normalizer test case')
	{
		$this->normalizer = new search_phone_number_normalizer();
		
		parent :: UnitTestCase($name);
	} 
		
	function test_process()
	{	
		$result = $this->normalizer->process('���.+7
			(8412)<b>5689-456-67</b>');
		
		$this->assertEqual($result, '78412568945667 8412568945667 568945667');
	}
	
	function test_process_one_number()
	{	
		$result = $this->normalizer->process('234');
		
		$this->assertEqual($result, '234');
	}

	function test_process_empty()
	{	
		$result = $this->normalizer->process('<b>nothing at all</b>');
		
		$this->assertEqual($result, '');
	}

} 
?>