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
require_once(LIMB_STATS_DIR . '/registers/StatsSearchPhraseRegister.class.php');
require_once(LIMB_STATS_DIR . '/registers/StatsRequest.class.php');
require_once(LIMB_STATS_DIR . '/search_engine_rules/SearchEngineRegexRule.class.php');

Mock :: generate('SearchEngineRegexRule');

class StatsSearchPhraseRegisterTest extends LimbTestCase
{
  var $register = null;
  var $db = null;
  var $conn = null;

  function StatsSearchPhraseRegisterTest()
  {
    parent :: LimbTestCase(__FILE__);

    $toolkit =& Limb :: toolkit();
    $this->conn =& $toolkit->getDbConnection();
    $this->db =& new SimpleDb($this->conn);
  }

  function setUp()
  {
    $this->register = new StatsSearchPhraseRegister();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('stats_search_phrase');
  }

  function testGetMatchingRuleOk()
  {
    $url = 'http://test.com';

    $rule_no_match = new MockSearchEngineRegexRule($this);
    $rule_no_match->expectOnce('match', array($url));
    $rule_no_match->setReturnValue('match', false);

    $rule_match = new MockSearchEngineRegexRule($this);
    $rule_match->expectOnce('match', array($url));
    $rule_match->setReturnValue('match', true);
    $rule_match->setReturnValue('getMatchingPhrase', 'test');

    $this->register->registerSearchEngineRule($rule_no_match);
    $this->register->registerSearchEngineRule($rule_match);

    $match_rule = $this->register->getMatchingSearchEngineRule(new Uri($url));
    $this->assertEqual($match_rule->getMatchingPhrase(), 'test');

    $rule_match->tally();
    $rule_no_match->tally();
  }

  function testGetMatchingRuleNull()
  {
    $url = 'http://test.com';
    $rule_no_match = new MockSearchEngineRegexRule($this);
    $rule_no_match->expectOnce('match', array($url));
    $rule_no_match->setReturnValue('match', false);

    $this->register->registerSearchEngineRule($rule_no_match);

    $uri = new Uri($url);
    $this->assertNull(null, $this->register->getMatchingSearchEngineRule($uri));

    $rule_no_match->tally();
  }

  function testRegisterOk()
  {
    $rule = new MockSearchEngineRegexRule($this);
    $rule->setReturnValue('getMatchingPhrase', $phrase = 'test');
    $rule->setReturnValue('getEngineName', $name = 'engine_name');
    $rule->expectOnce('match', array($url = 'http://example.com'));
    $rule->setReturnValue('match', true);

    $this->register->registerSearchEngineRule($rule);

    $stats_request = new StatsRequest();
    $stats_request->setTime($time = time());
    $stats_request->setRefererUri(new Uri($url));

    $this->assertTrue($this->register->register($stats_request));

    $rs =& $this->db->select('stats_search_phrase');

    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($record['phrase'], $phrase);
    $this->assertEqual($record['engine'], $name);
    $this->assertEqual($record['time'], $time);
  }

  function testRegisterFailedNoMatchingRule()
  {
    $rule = new MockSearchEngineRegexRule($this);
    $rule->setReturnValue('getMatchingPhrase', $phrase = 'test');
    $rule->setReturnValue('getEngineName', $name = 'engine_name');
    $rule->expectOnce('match', array($url = 'http://example.com'));
    $rule->setReturnValue('match', false);

    $this->register->registerSearchEngineRule($rule);

    $stats_request = new StatsRequest();
    $stats_request->setTime($time = time());
    $stats_request->setRefererUri(new Uri($url));

    $this->assertFalse($this->register->register($stats_request));

    $rs =& $this->db->select('stats_search_phrase');
    $this->assertEqual($rs->getRowCount(), 0);

  }
}

?>