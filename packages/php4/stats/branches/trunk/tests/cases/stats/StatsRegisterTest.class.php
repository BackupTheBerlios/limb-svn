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
require_once(dirname(__FILE__) . '/../../../StatsRegister.class.php');
require_once(dirname(__FILE__) . '/../../../StatsCounter.class.php');
require_once(dirname(__FILE__) . '/../../../StatsIp.class.php');
require_once(dirname(__FILE__) . '/../../../StatsReferer.class.php');
require_once(LIMB_DIR . '/class/request/Request.class.php');
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');

Mock :: generatePartial
(
  'StatsRegister',
  'StatsRegisterTestVersion',
  array(
    '_getIpRegister',
    '_getCounterRegister',
    '_getRefererRegister',
    '_getSearchPhraseRegister',
  )
);

Mock :: generatePartial
(
  'StatsCounter',
  'StatsCounterTestVersion2',
  array(
    'setNewHost',
    'update'
  )
);

Mock :: generatePartial
(
  'StatsIp',
  'StatsIpTestVersion',
  array(
    'getClientIp',
    'isNewHost',
  )
);

Mock :: generatePartial
(
  'StatsReferer',
  'StatsRefererTestVersion',
  array(
    'getRefererPageId'
  )
);

Mock :: generatePartial
(
  'StatsSearchPhrase',
  'StatsSearchPhraseTestVersion',
  array(
    'register'
  )
);

class StatsRegisterTest extends LimbTestCase
{
  var $db = null;

  var $stats_ip = null;

  var $stats_referer = null;

  var $stats_register = null;

  var $server = array();

  function statsRegisterTest()
  {
    parent :: LimbTestCase('stats register test');

    $this->db =& DbFactory :: instance();
  }

  function setUp()
  {
    $this->server = $_SERVER;
    $_SERVER['HTTP_HOST'] = 'test';

    $this->stats_counter = new StatsCounter();

    $this->stats_ip = new StatsIpTestVersion($this);
    $this->stats_ip->StatsIp();
    $this->stats_ip->setReturnValue('getClientIp', Ip :: encodeIp('127.0.0.1'));

    $this->stats_counter = new StatsCounterTestVersion2($this);
    $this->stats_counter->StatsCounter();

    $this->stats_referer = new StatsRefererTestVersion($this);
    $this->stats_referer->StatsReferer();
    $this->stats_referer->setReturnValue('getRefererPageId', 10);

    $this->stats_search_phrase = new StatsSearchPhraseTestVersion($this);
    $this->stats_search_phrase->StatsSearchPhrase();
    $this->stats_search_phrase->setReturnValue('register', true);

    $this->stats_register = new StatsRegisterTestVersion($this);
    $this->stats_register->StatsRegister();
    $this->stats_register->setReturnReference('_getIpRegister', $this->stats_ip);
    $this->stats_register->setReturnReference('_getCounterRegister', $this->stats_counter);
    $this->stats_register->setReturnReference('_getRefererRegister', $this->stats_referer);
    $this->stats_register->setReturnReference('_getSearchPhraseRegister', $this->stats_search_phrase);

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    $user->set('id', 10);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $_SERVER = $this->server;

    $this->stats_ip->tally();

    $this->stats_referer->tally();

    $this->stats_search_phrase->tally();

    $this->stats_register->tally();

    $inst =& User :: instance();
    $inst->logout();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->sqlDelete('sys_stat_log');
    $this->db->sqlDelete('sys_stat_ip');
    $this->db->sqlDelete('sys_stat_referer_url');
    $this->db->sqlDelete('sys_stat_counter');
    $this->db->sqlDelete('sys_stat_day_counters');
  }

  function testRegister()
  {
    $date = new Date();

    $this->stats_ip->expectOnce('isNewHost');
    $this->stats_ip->expectOnce('getClientIp');

    $this->stats_counter->expectOnce('setNewHost');
    $this->stats_counter->expectOnce('update', array($date));

    $this->stats_referer->expectOnce('getRefererPageId');

    $this->stats_search_phrase->expectOnce('register', array($date));

    $this->stats_register->setRegisterTime($date->getStamp());

    $this->stats_register->register($node_id = 1, 'test', LIMB_STATUS_OK);

    $this->_checkStatsRegisterRecord(
      $total_records = 1,
      $current_record = 1,
      $user_id = 10,
      $node_id,
      'test',
       LIMB_STATUS_OK,
      $this->stats_register->getRegisterTimeStamp());
  }

  function testCleanLog()
  {
    $this->stats_register->setRegisterTime(time());
    $this->stats_register->register($node_id = 1, 'test', LIMB_STATUS_OK);

    $this->stats_register->setRegisterTime(time() + 2*60*60*24);
    $this->stats_register->register($node_id = 1, 'test', LIMB_STATUS_OK);

    $this->stats_register->setRegisterTime(time() + 3*60*60*24);
    $this->stats_register->register($node_id = 1, 'test', LIMB_STATUS_OK);

    $this->stats_register->setRegisterTime(time() + 4*60*60*24);
    $this->stats_register->register($node_id = 1, 'test', LIMB_STATUS_OK);

    $this->stats_register->setRegisterTime(time() + 5*60*60*24);
    $this->stats_register->register($node_id = 1, 'test', LIMB_STATUS_OK);

    $this->stats_register->setRegisterTime(time() + 6*60*60*24);
    $this->stats_register->register($node_id = 1, 'test', LIMB_STATUS_OK);

    $date = new Date();
    $date->setByStamp(time() + 4*60*60*24 - 10);
    $this->stats_register->cleanUntil($date);

    $this->assertEqual(3, $this->stats_register->countLogRecords());
  }

  function _checkStatsRegisterRecord($total_records, $current_record, $user_id, $node_id, $action, $status, $time)
  {
    $this->db->sqlSelect('sys_stat_log', '*', '', 'id');
    $arr = $this->db->getArray();

    $this->assertTrue(sizeof($arr), $total_records);
    reset($arr);

    for($i = 1; $i <= $current_record; $i++)
    {
      $record = current($arr);
      next($arr);
    }

    $this->assertEqual($record['user_id'], $user_id);
    $this->assertEqual($record['node_id'], $node_id);
    $this->assertEqual($record['action'], $action);
    $this->assertEqual($record['status'], $status);
    $this->assertEqual($record['time'], $time, 'log time is incorrect');
    $this->assertEqual($record['session_id'], session_id());
  }
}

?>