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
require_once(dirname(__FILE__) . '/../../../../search_engine_rules/SearchEngineRamblerRule.class.php');

class SearchEngineRamblerRuleTest extends LimbTestCase
{
  var $rule = null;

  function searchEngineRamblerRuleTest()
  {
    parent :: LimbTestCase('rambler rule test');
  }

  function setUp()
  {
    $this->rule = new SearchEngineRamblerRule();
  }

  function testName()
  {
    $this->assertEqual('rambler', $this->rule->getEngineName());
  }

  function testMatchRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://search.rambler.ru/srch?words=%EF%F0%E8%E2%E5%F2&where=1')));
    $this->assertEqual('привет', $this->rule->getMatchingPhrase());
  }

  function testMatchEngTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://search.rambler.ru/srch?old_q=%EF%F0%E8%E2%E5%F2&words=wow&where=1')));
    $this->assertEqual('wow', $this->rule->getMatchingPhrase());
  }
}

?>