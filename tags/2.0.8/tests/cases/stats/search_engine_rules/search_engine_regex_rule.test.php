<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: referer.test.php 44 2004-03-17 18:03:28Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/model/stats/search_engine_rules/search_engine_regex_rule.class.php');

class test_search_engine_regex_rule extends UnitTestCase 
{
  var $rule = null;
	
  function test_search_engine_regex_rule() 
  {
  	parent :: UnitTestCase();
  }
  
  function setUp()
  {
   	$this->rule = new search_engine_regex_rule('google', '/^.*google\..*q=([^&]*).*$/', 1);
  }
   
  function test_match_true()
  {
  	$this->assertTrue($this->rule->match('http://www.google.com.ru/search?q=wow&some_other_parameter'));
  	
  	$this->assertEqual('google', $this->rule->get_engine_name());
  	$this->assertEqual('wow', $this->rule->get_matching_phrase());
  	
  }  
}

?>