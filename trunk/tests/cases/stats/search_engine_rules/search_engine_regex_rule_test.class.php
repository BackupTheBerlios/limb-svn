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
require_once(LIMB_DIR . '/core/model/stats/search_engine_rules/search_engine_regex_rule.class.php');

class search_engine_regex_rule_test extends LimbTestCase 
{
  var $rule = null;
	
  function search_engine_regex_rule_test() 
  {
  	parent :: LimbTestCase();
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