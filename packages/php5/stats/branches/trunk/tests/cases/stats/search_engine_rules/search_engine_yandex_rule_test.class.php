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
require_once(dirname(__FILE__) . '/../../../../search_engine_rules/search_engine_yandex_rule.class.php');

class search_engine_yandex_rule_test extends LimbTestCase 
{
  var $rule = null;
	
  function search_engine_yandex_rule_test() 
  {
  	parent :: LimbTestCase();
  }
  
  function setUp()
  {
   	$this->rule = new search_engine_yandex_rule();
  }
  
  function test_name()
  {
  	$this->assertEqual('yandex', $this->rule->get_engine_name());
  }
   
  function test_match_ru_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://www.yandex.ru/yandsearch?text=%EF%F0%E8%E2%E5%F2&stype=www')));
  	$this->assertEqual('привет', $this->rule->get_matching_phrase());
  } 

  function test_match_highlite_ru_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://hghltd.yandex.ru/yandbtm?url=http://limb-project.com/root/ru/portfolio/gallery/565%3FPHPSESSID%3Df13597251e617971f3710bbca63a96bf&text=%E1%FE%F0%EE+%E8%ED%F4%EE%F0%EC%E0%F6%E8%EE%ED%ED%FB%F5+%F2%E5%F5%ED%EE%EB%EE%E3%E8%E9&dsn=39&d=128984')));
  	$this->assertEqual('бюро информационных технологий', $this->rule->get_matching_phrase());
  } 

  function test_yandpage_match_ru_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://www.yandex.ru/yandpage?q=1051481360&p=1&ag=d&qs=%26text%3D%25D0%25D2%25C9%25D7%25C5%25D4')));
  	$this->assertEqual('привет', $this->rule->get_matching_phrase());
  } 
  
  function test_match_eng_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://www.yandex.ru/yandsearch?text=wow&stype=www')));
  	$this->assertEqual('wow', $this->rule->get_matching_phrase());
  } 

  function test_yandpage_match_eng_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://www.yandex.ru/yandpage?q=1164101856&p=3&ag=d&qs=%26text%3Dwow')));
  	$this->assertEqual('wow', $this->rule->get_matching_phrase());
  } 

}

?>