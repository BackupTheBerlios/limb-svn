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
require_once(dirname(__FILE__) . '/../../StatsCounterRegister.class.php');
require_once(dirname(__FILE__) . '/../../StatsRequest.class.php');
require_once(dirname(__FILE__) . '/../../StatsIp.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

Mock :: generate('StatsIp');

class StatsCounterRegisterTest extends LimbTestCase
{
  var $db = null;
  var $conn = null;

  var $ip_register = null;
  var $register = null;

  function StatsCounterRegisterTest()
  {
    parent :: LimbTestCase('stats counter register test');
  }

  function setUp()
  {
    $this->conn =& LimbDbPool :: getConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->ip_register = new MockStatsIp($this);

    $this->register = new StatsCounterRegister();
    $this->register->setIpRegister($this->ip_register);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->ip_register->tally();

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('stats_counter');
    $this->db->delete('stats_day_counters');
  }

  function testNewHost()
  {
    $stats_request = new StatsRequest();
    $stats_request->setTime($time = time());
    $stats_request->setUri(new Uri('http://test.com'));
    $stats_request->setRefererUri(new Uri('http://example.com'));
    $stats_request->setClientIp($ip = '127.0.0.1');

    $this->ip_register->expectOnce('isNewToday', array($ip));
    $this->ip_register->setReturnValue('isNewToday', true);

    $this->register->register($stats_request);

    $this->_checkStatsCounterRecord(
      $hits_all = 1,
      $hits_today = 1,
      $hosts_all = 1,
      $hosts_today = 1,
      $time);

    $this->_checkStatsDayCountersRecord(
      $hits_today,
      $hosts_today,
      $home_hits = 0,
      $audience_host = 0,
      $time);

    $this->_checkCountersConsistency($time);
  }

  function testSameDay()
  {
    $stats_request = new StatsRequest();
    $stats_request->setTime($time = time());
    $stats_request->setUri(new Uri('http://test.com'));
    $stats_request->setRefererUri(new Uri('http://example.com'));

    $this->ip_register->setReturnValue('isNewToday', true);

    $this->register->register($stats_request);
    $this->register->register($stats_request);

    $this->_checkStatsCounterRecord(
      $hits_all = 2,
      $hits_today = 2,
      $hosts_all = 2,
      $hosts_today = 2,
      $time);

    $this->_checkStatsDayCountersRecord(
      $hits_today,
      $hosts_today,
      $home_hits = 0,
      $audience_host = 0,
      $time);

    $this->_checkCountersConsistency($time);
  }

  function testNewDay()
  {
    $stats_request = new StatsRequest();
    $stats_request->setTime($time = time());
    $stats_request->setUri(new Uri('http://test.com'));
    $stats_request->setRefererUri(new Uri('http://example.com'));

    $this->ip_register->setReturnValue('isNewToday', true);

    $date = new Date();
    $date->setByStamp($time);
    $this->register->register($stats_request);

    $date->setByDays($date->dateToDays() + 1);
    $stats_request->setTime($date->getStamp());
    $this->register->register($stats_request);

    $this->_checkStatsCounterRecord(
      $hits_all = 2,
      $hits_today = 1,
      $hosts_all = 2,
      $hosts_today = 1,
     $date->getStamp());

    $this->_checkStatsDayCountersRecord(
      $hits_today,
      $hosts_today,
      $home_hits = 0,
      $audience_host = 0,
      $time);

    $this->_checkCountersConsistency($time);
  }

  function testNewAudience()
  {
    $stats_request = new StatsRequest();
    $stats_request->setTime($time = time());
    $stats_request->setUri(new Uri('http://test.com'));

    $this->ip_register->setReturnValue('isNewToday', true);

    $this->register->register($stats_request);

    $this->_checkStatsDayCountersRecord(
      $hits_today = 1,
      $hosts_today = 1,
      $home_hits = 0,
      $audience_host = 1,
      $time);
  }

  function testNewAudienceSameHost()
  {
    $stats_request = new StatsRequest();
    $stats_request->setTime($time = time());
    $stats_request->setUri(new Uri('http://test.com'));

    $this->ip_register->setReturnValue('isNewToday', false);

    $this->register->register($stats_request);

    $this->_checkStatsDayCountersRecord(
      $hits_today = 1,
      $hosts_today = 0,
      $home_hits = 0,
      $audience_host = 0,
      $time);
  }

  function testHomeHit()
  {
    $stats_request = new StatsRequest();
    $stats_request->setTime($time = time());
    $stats_request->setUri(new Uri('http://test.com'));
    $stats_request->setBaseUri(new Uri('http://test.com'));

    $this->ip_register->setReturnValue('isNewToday', false);

    $this->register->register($stats_request);

    $this->_checkStatsDayCountersRecord(
      $hits_today = 1,
      $hosts_today = 0,
      $home_hits = 1,
      $audience_host = 0,
      $time);
  }

  function testGetDefaultIpRegister()
  {
    $register = new StatsCounterRegister();

    $this->assertTrue(is_a($register->getIpRegister(), 'StatsIp'));

  }

  function _checkStatsCounterRecord($hits_all, $hits_today, $hosts_all, $hosts_today, $time)
  {
    $rs =& $this->db->select('stats_counter', '*');

    $record = $rs->getRow();

    $this->assertNotIdentical($record, false, 'counter record doesnt exist');
    $this->assertEqual($record['hits_all'], $hits_all, 'all hits incorrect. Got ' . $record['hits_all'] . ', expected '. $hits_all);
    $this->assertEqual($record['hits_today'], $hits_today, 'today hits incorrect. Got ' . $record['hits_today'] . ', expected '. $hits_today);
    $this->assertEqual($record['hosts_all'], $hosts_all, 'all hosts incorrect. Got ' . $record['hosts_all'] . ', expected '. $hosts_all);
    $this->assertEqual($record['hosts_today'], $hosts_today, 'today hosts incorrect. Got ' . $record['hosts_today'] . ', expected '. $hosts_today);
    $this->assertEqual($record['time'], $time, 'counter time is incorrect. Got ' . $record['time'] . ', expected '. $time);
  }

  function _checkStatsDayCountersRecord($hits, $hosts, $home_hits, $audience_hosts, $time)
  {
    $rs =& $this->db->select('stats_day_counters',
                      '*',
                      array('time' => $this->register->makeDayStamp($time)));

    $record = $rs->getRow();

    $this->assertNotIdentical($record, false, 'day counters record doesnt exist');
    $this->assertEqual($record['hits'], $hits, 'day hits incorrect. Got ' . $record['hits'] . ', expected '. $hits);
    $this->assertEqual($record['hosts'], $hosts, 'day hits incorrect. Got ' . $record['hosts'] . ', expected '. $hosts);
    $this->assertEqual($record['home_hits'], $home_hits, 'day home hits incorrect. Got ' . $record['home_hits'] . ', expected '. $home_hits);
    $this->assertEqual($record['audience'], $audience_hosts, 'audience incorrect. Got ' . $record['audience'] . ', expected '. $audience_hosts);
  }

  function _checkCountersConsistency($time)
  {
    $sql = 'SELECT SUM(hits) as hits_all, SUM(hosts) as hosts_all FROM stats_day_counters';

    $stmt =& $this->conn->newStatement($sql);
    $rs = new SimpleDbDataset($stmt->getRecordSet());
    $record1 = $rs->getRow();

    $rs =& $this->db->select('stats_counter', '*');
    $record2 = $rs->getRow();

    $this->assertEqual($record1['hits_all'],
                       $record2['hits_all'],
                       'Counters all hits number inconsistent. ' . $record1['hits_all'] . ' not equal '. $record2['hits_all']);

    $this->assertEqual($record1['hosts_all'],
                       $record2['hosts_all'],
                       'Counters all hosts number inconsistent. ' . $record1['hosts_all'] . ' not equal '. $record2['hosts_all']);

    $rs = $this->db->select('stats_day_counters',
                         '*',
                         array('time' => $this->register->makeDayStamp($time)));

    $record3 = $rs->getRow();

    $this->assertEqual($record3['hits'],
                       $record2['hits_today'],
                       'Counters day hits number inconsistent. ' . $record3['hits'] . ' not equal '. $record2['hits_today']);

    $this->assertEqual($record3['hosts'],
                       $record2['hosts_today'],
                       'Counters day hosts number inconsistent. ' . $record3['hosts'] . ' not equal '. $record2['hosts_today']);
  }
}

?>