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
require_once(dirname(__FILE__) . '/../../../../search_engine_rules/SearchEngineGoogleRule.class.php');

class SearchEngineGoogleRuleTest extends LimbTestCase
{
  var $rule = null;

  function searchEngineGoogleRuleTest()
  {
    parent :: LimbTestCase();
  }

  function setUp()
  {
    $this->rule = new SearchEngineGoogleRule();
  }

  function testName()
  {
    $this->assertEqual('google', $this->rule->getEngineName());
  }

  function testMatchRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://www.google.com.ru/search?q=%D0%BF%D1%80%D0%B8%D0%B2%D0%B5%D1%82&ie=UTF-8&oe=UTF-8&hl=ru&lr=')));
    $this->assertEqual('привет', $this->rule->getMatchingPhrase());
  }

  function testMatchCachedRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://www.google.com.ru/search?q=cache:jo_5sCW8-B0J:www.0x00.ru/+%D0%B1%D1%8E%D1%80%D0%BE+%D0%B8%D0%BD%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%86%D0%B8%D0%BE%D0%BD%D0%BD%D1%8B%D1%85+%D1%82%D0%B5%D1%85%D0%BD%D0%BE%D0%BB%D0%BE%D0%B3%D0%B8%D0%B9&hl=ru&ie=UTF-8')));
    $this->assertEqual('бюро информационных технологий', $this->rule->getMatchingPhrase());
  }

  function testMatchEngTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://www.google.com.ru/search?q=wow&ie=UTF-8&oe=UTF-8&hl=ru&lr=')));
    $this->assertEqual('wow', $this->rule->getMatchingPhrase());
  }

}

?>