<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/../../../../search_engine_rules/search_engine_yahoo_rule.class.php');

class search_engine_yahoo_rule_test extends LimbTestCase
{
  var $rule = null;

  function search_engine_yahoo_rule_test()
  {
    parent :: LimbTestCase();
  }

  function setUp()
  {
    $this->rule = new search_engine_yahoo_rule();
  }

  function test_name()
  {
    $this->assertEqual('yahoo', $this->rule->get_engine_name());
  }

  function test_match_ru_true()
  {
    $this->assertTrue($this->rule->match(urldecode('http://search.yahoo.com/search?p=%D0%BF%D1%80%D0%B8%D0%B2%D0%B5%D1%82+%D0%BF%D1%80%D0%B8%D0%B2%D0%B5%D1%82&ei=UTF-8&fr=fp-tab-web-t&cop=mss&tab=')));
    $this->assertEqual('привет привет', $this->rule->get_matching_phrase());
  }

  function test_match_second_ru_true()
  {
    $this->assertTrue($this->rule->match(urldecode('http://search.yahoo.com/search?p=%d0%bf%d1%80%d0%b8%d0%b2%d0%b5%d1%82&ei=UTF-8&cop=mss&fr=fp-tab-web-t&b=21')));
    $this->assertEqual('привет', $this->rule->get_matching_phrase());
  }

  function test_match_eng_true()
  {
    $this->assertTrue($this->rule->match(urldecode('http://search.yahoo.com/search?p=wow&ei=UTF-8&n=20&fl=0&fr=fp-tab-web-t&b=181')));
    $this->assertEqual('wow', $this->rule->get_matching_phrase());
  }
}

?>