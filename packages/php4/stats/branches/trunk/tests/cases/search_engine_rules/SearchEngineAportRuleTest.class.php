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
require_once(dirname(__FILE__) . '/../../../search_engine_rules/SearchEngineAportRule.class.php');

class SearchEngineAportRuleTest extends LimbTestCase
{
  var $rule = null;

  function searchEngineAportRuleTest()
  {
    parent :: LimbTestCase(__FILE__);
  }

  function setUp()
  {
    $this->rule = new SearchEngineAportRule();
  }

  function testName()
  {
    $this->assertEqual('aport', $this->rule->getEngineName());
  }

  function testMatchRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://sm.aport.ru/scripts/template.dll?r=%EF%F0%E8%E2%E5%F2&That=std')));
    $this->assertEqual('привет', $this->rule->getMatchingPhrase());
  }

  function testMatchSecondRuTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://sm.aport.ru/scripts/template.dll?r=%EF%F0%E8%E2%E5%F2&id=62964315&That=std&p=1&HID=1_2_3_4_5_6_7_8_9_10_11_12_13')));
    $this->assertEqual('привет', $this->rule->getMatchingPhrase());
  }

  function testMatchEngTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://sm.aport.ru/scripts/template.dll?r=wow&That=std')));
    $this->assertEqual('wow', $this->rule->getMatchingPhrase());
  }

  function testMatchSecondEngTrue()
  {
    $this->assertTrue($this->rule->match(urldecode('http://sm.aport.ru/scripts/template.dll?r=wow&id=62964315&That=std&p=1&HID=1_2_3_4_5_6_7_8_9_10_11_12_13')));
    $this->assertEqual('wow', $this->rule->getMatchingPhrase());
  }
}

?>