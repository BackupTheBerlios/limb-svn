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
require_once(dirname(__FILE__) . '/../../StatsRegister.class.php');
require_once(dirname(__FILE__) . '/../../StatsCounter.class.php');
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

class StatsCounterTest extends LimbTestCase
{
  var $db = null;
  var $conn = null;

  var $stats_counter = null;

  function StatsCounterTest()
  {
    parent :: LimbTestCase('stats counter test');
  }

  function setUp()
  {
    $this->conn =& LimbDbPool :: getConnection();
    $this->db =& new SimpleDb($this->conn);

    $this->stats_counter = new StatsCounter();

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('stat_counter');
    $this->db->delete('stat_day_counters');
  }

  function testNewHost()
  {
    $date = new Date();

    $this->stats_counter->setNewHost();

    $this->stats_counter->update($date);

    $this->_checkStatsCounterRecord(
      $hits_all = 1,
      $hits_today = 1,
      $hosts_all = 1,
      $hosts_today = 1,
      $date);

    $this->_checkStatsDayCountersRecord(
      $hits_today,
      $hosts_today,
      $home_hits = 0,
      $audience_host = 0,
      $date);

    $this->_checkCountersConsistency($date);
  }

  function testNewHostSameDay()
  {
    $this->testNewHost();

    $date = new Date();
    $this->stats_counter->setNewHost();
    $this->stats_counter->update($date);

    $this->_checkStatsCounterRecord(
      $hits_all = 2,
      $hits_today = 2,
      $hosts_all = 2,
      $hosts_today = 2,
      $date);

    $this->_checkStatsDayCountersRecord(
      $hits_today,
      $hosts_today,
      $home_hits = 0,
      $audience_host = 0,
      $date
    );

    $this->_checkCountersConsistency($date);
  }

  function testNewHostNewDay()
  {
    $this->testNewHost();

    $date = new Date();
    $date->setByDays($date->dateToDays() + 1);
    $this->stats_counter->setNewHost();
    $this->stats_counter->update($date);

    $this->_checkStatsCounterRecord(
      $hits_all = 2,
      $hits_today = 1,
      $hosts_all = 2,
      $hosts_today = 1,
      $date);

    $this->_checkStatsDayCountersRecord(
      $hits_today,
      $hosts_today,
      $home_hits = 0,
      $audience_host = 0,
      $date
    );

    $this->_checkCountersConsistency($date);
  }

  function testNewAudience()
  {
    $date = new Date();
    $this->stats_counter->setNewHost();

    $old_server_value = $_SERVER;

    unset($_SERVER['HTTP_REFERER']);

    $this->stats_counter->update($date);

    $this->_checkStatsDayCountersRecord(
      $hits_today = 1,
      $hosts_today = 1,
      $home_hits = 0,
      $audience_host = 1,
      $date
    );

    $_SERVER = $old_server_value;
  }

  function testNewAudienceSameHost()
  {
    $date = new Date();

    $old_server_value = $_SERVER;

    $_SERVER['HTTP_REFERER'] = 'some referer';

    $this->stats_counter->update($date);

    $this->_checkStatsDayCountersRecord(
      $hits_today = 1,
      $hosts_today = 0,
      $home_hits = 0,
      $audience_host = 0,
      $date
    );

    $old_server_value = $_SERVER;
  }

  function testHomeHit()
  {
    $date = new Date();
    $this->stats_counter->setHomeHit();

    $this->stats_counter->update($date);

    $this->_checkStatsDayCountersRecord(
      $hits_today = 1,
      $hosts_today = 0,
      $home_hits = 1,
      $audience_host = 0,
      $date
    );
  }

  function _checkStatsCounterRecord($hits_all, $hits_today, $hosts_all, $hosts_today, $date)
  {
    $rs =& $this->db->select('stat_counter', '*');

    $record = $rs->getRow();

    $this->assertNotIdentical($record, false, 'counter record doesnt exist');
    $this->assertEqual($record['hits_all'], $hits_all, 'all hits incorrect. Got ' . $record['hits_all'] . ', expected '. $hits_all);
    $this->assertEqual($record['hits_today'], $hits_today, 'today hits incorrect. Got ' . $record['hits_today'] . ', expected '. $hits_today);
    $this->assertEqual($record['hosts_all'], $hosts_all, 'all hosts incorrect. Got ' . $record['hosts_all'] . ', expected '. $hosts_all);
    $this->assertEqual($record['hosts_today'], $hosts_today, 'today hosts incorrect. Got ' . $record['hosts_today'] . ', expected '. $hosts_today);
    $this->assertEqual($record['time'], $date->getStamp(), 'counter time is incorrect. Got ' . $record['time'] . ', expected '. $date->getStamp());
  }

  function _checkStatsDayCountersRecord($hits, $hosts, $home_hits, $audience_hosts, $date)
  {
    $rs =& $this->db->select('stat_day_counters',
                      '*',
                      array('time' => $this->stats_counter->makeDayStamp($date->getStamp())));

    $record = $rs->getRow();

    $this->assertNotIdentical($record, false, 'day counters record doesnt exist');
    $this->assertEqual($record['hits'], $hits, 'day hits incorrect. Got ' . $record['hits'] . ', expected '. $hits);
    $this->assertEqual($record['hosts'], $hosts, 'day hits incorrect. Got ' . $record['hosts'] . ', expected '. $hosts);
    $this->assertEqual($record['home_hits'], $home_hits, 'day home hits incorrect. Got ' . $record['home_hits'] . ', expected '. $home_hits);
    $this->assertEqual($record['audience'], $audience_hosts, 'audience incorrect. Got ' . $record['audience'] . ', expected '. $audience_hosts);
  }

  function _checkCountersConsistency($date)
  {
    $time = $date->getStamp();

    $sql = 'SELECT SUM(hits) as hits_all, SUM(hosts) as hosts_all FROM stat_day_counters';

    $stmt =& $this->conn->newStatement($sql);
    $rs = new SimpleDbDataset($stmt->getRecordSet());
    $record1 = $rs->getRow();

    $rs =& $this->db->select('stat_counter', '*');
    $record2 = $rs->getRow();

    $this->assertEqual($record1['hits_all'],
                       $record2['hits_all'],
                       'Counters all hits number inconsistent. ' . $record1['hits_all'] . ' not equal '. $record2['hits_all']);

    $this->assertEqual($record1['hosts_all'],
                       $record2['hosts_all'],
                       'Counters all hosts number inconsistent. ' . $record1['hosts_all'] . ' not equal '. $record2['hosts_all']);

    $rs = $this->db->select('stat_day_counters',
                         '*',
                         array('time' => $this->stats_counter->makeDayStamp($time)));

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