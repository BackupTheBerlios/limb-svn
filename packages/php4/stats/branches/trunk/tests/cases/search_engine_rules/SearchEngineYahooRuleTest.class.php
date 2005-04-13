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
require_once(dirname(__FILE__) . '/../../../search_engine_rules/SearchEngineYahooRule.class.php');

class SearchEngineYahooRuleTest extends LimbTestCase
{
  var $rule = null;

  function searchEngineYahooRuleTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->rule = new SearchEngineYahooRule();
  }

  function testName()
  {
    $this->assertEqual('yahoo', $this->rule->getEngineName());
  }

  function testMatchRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://search.yahoo.com/search?p=%D0%BF%D1%80%D0%B8%D0%B2%D0%B5%D1%82+%D0%BF%D1%80%D0%B8%D0%B2%D0%B5%D1%82&ei=UTF-8&fr=fp-tab-web-t&cop=mss&tab=')));
    $this->assertEqual('привет привет', $this->rule->getMatchingPhrase());
  }

  function testMatchSecondRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://search.yahoo.com/search?p=%d0%bf%d1%80%d0%b8%d0%b2%d0%b5%d1%82&ei=UTF-8&cop=mss&fr=fp-tab-web-t&b=21')));
    $this->assertEqual('привет', $this->rule->getMatchingPhrase());
  }

  function testMatchEngTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://search.yahoo.com/search?p=wow&ei=UTF-8&n=20&fl=0&fr=fp-tab-web-t&b=181')));
    $this->assertEqual('wow', $this->rule->getMatchingPhrase());
  }
}

?>