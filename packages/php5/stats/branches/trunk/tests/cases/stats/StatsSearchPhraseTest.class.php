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
require_once(dirname(__FILE__) . '/../../../StatsSearchPhrase.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');

Mock :: generate('SearchEngineRegexRule');

Mock :: generatePartial
(
  'StatsSearchPhrase',
  'StatsSearchPhraseSelfTestVersion',
  array(
    '_getHttpReferer',
  )
);

class StatsSearchPhraseTest extends LimbTestCase
{
  var $stats_referer = null;
  var $db = null;

  function statsSearchPhraseTest()
  {
    parent :: limbTestCase();

    $this->db = DbFactory :: instance();
  }

  function setUp()
  {
    $this->stats_search_phrase = new StatsSearchPhraseSelfTestVersion($this);
    $this->stats_search_phrase->__construct();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->stats_search_phrase->tally();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('sys_stat_search_phrase');
  }

  function testGetMatchingRuleOk()
  {
    $this->stats_search_phrase->setReturnValue('_getHttpReferer', 'test');

    $rule_no_match = new MockSearchEngineRegexRule($this);
    $rule_no_match->expectOnce('match');
    $rule_no_match->setReturnValue('match', false);

    $rule_match = new MockSearchEngineRegexRule($this);
    $rule_match->expectOnce('match');
    $rule_match->setReturnValue('match', true);
    $rule_match->setReturnValue('getMatchingPhrase', 'test');

    $this->stats_search_phrase->registerSearchEngineRule($rule_no_match);
    $this->stats_search_phrase->registerSearchEngineRule($rule_match);

    $match_rule = $this->stats_search_phrase->getMatchingSearchEngineRule();
    $this->assertEqual($match_rule->getMatchingPhrase(), 'test');

    $rule_match->tally();
    $rule_no_match->tally();
  }

  function testGetMatchingRuleNull()
  {
    $this->stats_search_phrase->setReturnValue('_getHttpReferer', 'test');

    $rule_no_match = new MockSearchEngineRegexRule($this);
    $rule_no_match->expectOnce('match');
    $rule_no_match->setReturnValue('match', false);

    $this->stats_search_phrase->registerSearchEngineRule($rule_no_match);

    $this->assertNull(null, $this->stats_search_phrase->getMatchingSearchEngineRule());

    $rule_no_match->tally();
  }

  function testRegister()
  {
    $rule_match = new MockSearchEngineRegexRule($this);
    $rule_match->setReturnValue('match', true);
    $rule_match->setReturnValue('getMatchingPhrase', 'test');
    $rule_match->setReturnValue('getEngineName', 'engineName');

    $this->stats_search_phrase->registerSearchEngineRule($rule_match);

    $date = new Date();
    $this->assertTrue($this->stats_search_phrase->register($date));

    $db = DbFactory :: instance();
    $db->sqlSelect('sys_stat_search_phrase');

    $arr = $db->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $record = current($arr);

    $this->assertEqual($record['phrase'], 'test');
    $this->assertEqual($record['engine'], 'engine_name');
    $this->assertEqual($record['time'], $date->getStamp());
  }
}

?>