<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(dirname(__FILE__) . '/../../../stats_search_phrase.class.php');

Mock::generate('search_engine_regex_rule');

Mock::generatePartial
(
  'stats_search_phrase',
  'stats_search_phrase_self_test_version',
  array(
  	'_get_http_referer',
  )
);

class stats_search_phrase_test extends LimbTestCase 
{
  var $stats_referer = null;
  var $db = null;
	
  function stats_search_phrase_test() 
  {
  	parent :: LimbTestCase();
  	
  	$this->db =& db_factory :: instance();
  }
  
  function setUp()
  {
   	$this->stats_search_phrase = new stats_search_phrase_self_test_version($this);
   	$this->stats_search_phrase->__construct();
   	
   	$this->_clean_up();
  }
  
  function tearDown()
  {
  	$this->stats_search_phrase->tally();
  	
  	$this->_clean_up();
  }
  
  function _clean_up()
  {
  	$this->db->sql_delete('sys_stat_search_phrase');
  }
  
  function test_get_matching_rule_ok()
  {
  	$this->stats_search_phrase->setReturnValue('_get_http_referer', 'test');

  	$rule_no_match =& new Mocksearch_engine_regex_rule($this);
  	$rule_no_match->expectOnce('match');
  	$rule_no_match->setReturnValue('match', false);
  	
  	$rule_match =& new Mocksearch_engine_regex_rule($this);
  	$rule_match->expectOnce('match');
  	$rule_match->setReturnValue('match', true);
  	$rule_match->setReturnValue('get_matching_phrase', 'test');
  	
  	$this->stats_search_phrase->register_search_engine_rule($rule_no_match);
  	$this->stats_search_phrase->register_search_engine_rule($rule_match);
  	
  	$match_rule = $this->stats_search_phrase->_get_matching_search_engine_rule();
  	$this->assertEqual($match_rule->get_matching_phrase(), 'test');
  	
  	$rule_match->tally();
  	$rule_no_match->tally();
  }

  function test_get_matching_rule_null()
  {
  	$this->stats_search_phrase->setReturnValue('_get_http_referer', 'test');

  	$rule_no_match =& new Mocksearch_engine_regex_rule($this);
  	$rule_no_match->expectOnce('match');
  	$rule_no_match->setReturnValue('match', false);
  	  	
  	$this->stats_search_phrase->register_search_engine_rule($rule_no_match);
  	
  	$this->assertNull(null, $this->stats_search_phrase->_get_matching_search_engine_rule());
  	
  	$rule_no_match->tally();
  }
  
  function test_register()
  {
  	$rule_match =& new Mocksearch_engine_regex_rule($this);
  	$rule_match->setReturnValue('match', true);
  	$rule_match->setReturnValue('get_matching_phrase', 'test');
  	$rule_match->setReturnValue('get_engine_name', 'engine_name');

  	$this->stats_search_phrase->register_search_engine_rule($rule_match);
  	
  	$date = new date();
  	$this->assertTrue($this->stats_search_phrase->register($date));
  	
  	$db =& db_factory :: instance();
  	$db->sql_select('sys_stat_search_phrase');
  	
  	$arr = $db->get_array();
  	
  	$this->assertEqual(sizeof($arr), 1);
  	$record = current($arr);
  	
  	$this->assertEqual($record['phrase'], 'test');
  	$this->assertEqual($record['engine'], 'engine_name');
  	$this->assertEqual($record['time'], $date->get_stamp());
  }
}

?>