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
require_once(dirname(__FILE__) . '/../../../../search_engine_rules/SearchEngineMailruRule.class.php');

class SearchEngineMailruRuleTest extends LimbTestCase
{
  var $rule = null;

  function searchEngineMailruRuleTest()
  {
    parent :: LimbTestCase();
  }

  function setUp()
  {
    $this->rule = new SearchEngineMailruRule();
  }

  function testName()
  {
    $this->assertEqual('mail.ru', $this->rule->getEngineName());
  }

  function testMatchRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/?qs=1&lfilter=yes&words=%EF%F0%E8%E2%E5%F2&change=2')));
    $this->assertEqual('привет', $this->rule->getMatchingPhrase());
  }

  function testSecondMatchRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/index.phtml?lfilter=yes&hl=ru&q=%EF%F0%E8%E2%E5%F2&change=2')));
    $this->assertEqual('привет', $this->rule->getMatchingPhrase());
  }

  function testMatchEngTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/?qs=1&lfilter=yes&words=wow&change=2')));
    $this->assertEqual('wow', $this->rule->getMatchingPhrase());
  }

  function testSecondMatchEngTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://go.mail.ru/index.phtml?lfilter=yes&hl=ru&q=wow&change=2')));
    $this->assertEqual('wow', $this->rule->getMatchingPhrase());
  }
}

?>