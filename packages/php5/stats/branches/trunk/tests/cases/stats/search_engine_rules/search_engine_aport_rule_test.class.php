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
require_once(dirname(__FILE__) . '/../../../../search_engine_rules/search_engine_aport_rule.class.php');

class search_engine_aport_rule_test extends LimbTestCase 
{
  var $rule = null;
	
  function search_engine_aport_rule_test() 
  {
  	parent :: LimbTestCase();
  }
  
  function setUp()
  {
   	$this->rule = new search_engine_aport_rule();
  }
  
  function test_name()
  {
  	$this->assertEqual('aport', $this->rule->get_engine_name());
  }
   
  function test_match_ru_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://sm.aport.ru/scripts/template.dll?r=%EF%F0%E8%E2%E5%F2&That=std')));
  	$this->assertEqual('привет', $this->rule->get_matching_phrase());
  } 
  
  function test_match_second_ru_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://sm.aport.ru/scripts/template.dll?r=%EF%F0%E8%E2%E5%F2&id=62964315&That=std&p=1&HID=1_2_3_4_5_6_7_8_9_10_11_12_13')));
  	$this->assertEqual('привет', $this->rule->get_matching_phrase());
  } 

  function test_match_eng_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://sm.aport.ru/scripts/template.dll?r=wow&That=std')));
  	$this->assertEqual('wow', $this->rule->get_matching_phrase());
  } 
  
  function test_match_second_eng_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://sm.aport.ru/scripts/template.dll?r=wow&id=62964315&That=std&p=1&HID=1_2_3_4_5_6_7_8_9_10_11_12_13')));
  	$this->assertEqual('wow', $this->rule->get_matching_phrase());
  } 
}

?>