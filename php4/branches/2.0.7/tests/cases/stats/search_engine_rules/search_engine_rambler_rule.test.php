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
require_once(LIMB_DIR . '/core/model/stats/search_engine_rules/search_engine_rambler_rule.class.php');

class test_search_engine_rambler_rule extends UnitTestCase 
{
  var $rule = null;
	
  function test_search_engine_rambler_rule() 
  {
  	parent :: UnitTestCase();
  }
  
  function setUp()
  {
   	$this->rule = new search_engine_rambler_rule();
  }
  
  function test_name()
  {
  	$this->assertEqual('rambler', $this->rule->get_engine_name());
  }
   
  function test_match_ru_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://search.rambler.ru/srch?words=%EF%F0%E8%E2%E5%F2&where=1')));
  	$this->assertEqual('привет', $this->rule->get_matching_phrase());
  } 

  function test_match_eng_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://search.rambler.ru/srch?old_q=%EF%F0%E8%E2%E5%F2&words=wow&where=1')));
  	$this->assertEqual('wow', $this->rule->get_matching_phrase());
  } 
}

?>