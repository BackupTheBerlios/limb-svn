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
require_once(LIMB_DIR . '/core/model/stats/search_engine_rules/search_engine_mailru_rule.class.php');

class test_search_engine_mailru_rule extends UnitTestCase 
{
  var $rule = null;
	
  function test_search_engine_mailru_rule() 
  {
  	parent :: UnitTestCase();
  }
  
  function setUp()
  {
   	$this->rule = new search_engine_mailru_rule();
  }
  
  function test_name()
  {
  	$this->assertEqual('mail.ru', $this->rule->get_engine_name());
  }
   
  function test_match_ru_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/?qs=1&lfilter=yes&words=%EF%F0%E8%E2%E5%F2&change=2')));
  	$this->assertEqual('привет', $this->rule->get_matching_phrase());
  } 

  function test_second_match_ru_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/index.phtml?lfilter=yes&hl=ru&q=%EF%F0%E8%E2%E5%F2&change=2')));
  	$this->assertEqual('привет', $this->rule->get_matching_phrase());
  } 

  function test_match_eng_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/?qs=1&lfilter=yes&words=wow&change=2')));
  	$this->assertEqual('wow', $this->rule->get_matching_phrase());
  } 

  function test_second_match_eng_true()
  {
  	$this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/index.phtml?lfilter=yes&hl=ru&q=wow&change=2')));
  	$this->assertEqual('wow', $this->rule->get_matching_phrase());
  } 
}

?>