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
require_once(dirname(__FILE__) . '/../../../../search_engine_rules/SearchEngineRegexRule.class.php');

class SearchEngineRegexRuleTest extends LimbTestCase
{
  var $rule = null;

  function searchEngineRegexRuleTest()
  {
    parent :: LimbTestCase('search engine regex test');
  }

  function setUp()
  {
    $this->rule = new SearchEngineRegexRule('google', '/^.*google\..*q=([^&]*).*$/', 1);
  }

  function testMatchTrue()
  {
    $this->assertTrue($this->rule->match('http://www.google.com.ru/search?q=wow&some_other_parameter'));

    $this->assertEqual('google', $this->rule->getEngineName());
    $this->assertEqual('wow', $this->rule->getMatchingPhrase());

  }
}

?>