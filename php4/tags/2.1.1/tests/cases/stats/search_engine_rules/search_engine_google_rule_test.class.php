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
require_once(LIMB_DIR . '/core/model/stats/search_engine_rules/search_engine_google_rule.class.php');

class search_engine_google_rule_test extends UnitTestCase 
{
  var $rule = null;
	
  function search_engine_google_rule_test() 
  {
  	parent :: UnitTestCase();
  }
  
  function setUp()
  {
   	$this->rule = new search_engine_google_rule();
  }
  
  function test_name()
  {
  	$this->assertEqual('google', $this->rule->get_engine_name());
  }
   
  function test_match_ru_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://www.google.com.ru/search?q=%D0%BF%D1%80%D0%B8%D0%B2%D0%B5%D1%82&ie=UTF-8&oe=UTF-8&hl=ru&lr=')));
  	$this->assertEqual('привет', $this->rule->get_matching_phrase());
  }

  function test_match_cached_ru_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://www.google.com.ru/search?q=cache:jo_5sCW8-B0J:www.0x00.ru/+%D0%B1%D1%8E%D1%80%D0%BE+%D0%B8%D0%BD%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%86%D0%B8%D0%BE%D0%BD%D0%BD%D1%8B%D1%85+%D1%82%D0%B5%D1%85%D0%BD%D0%BE%D0%BB%D0%BE%D0%B3%D0%B8%D0%B9&hl=ru&ie=UTF-8')));
  	$this->assertEqual('бюро информационных технологий', $this->rule->get_matching_phrase());
  }
  
  function test_match_eng_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://www.google.com.ru/search?q=wow&ie=UTF-8&oe=UTF-8&hl=ru&lr=')));
  	$this->assertEqual('wow', $this->rule->get_matching_phrase());
  }  

}

?>