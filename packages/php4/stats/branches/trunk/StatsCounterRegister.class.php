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
require_once(LIMB_DIR . '/core/date/Date.class.php');

class StatsCounterRegister
{
  var $_hits_today;
  var $_hosts_today;
  var $_hits_all;
  var $_hosts_all;

  var $counter_db_table = null;
  var $day_counters_db_table = null;

  var $stats_ip = null;

  function StatsCounterRegister()
  {
    $toolkit =& Limb :: toolkit();
    $this->counter_db_table =& $toolkit->createDBTable('StatsCounter');
    $this->day_counters_db_table =& $toolkit->createDBTable('StatsDayCounters');
  }

  function register(&$stats_request, $is_new_host = false)
  {
    $reg_date = new Date();
    $time = $stats_request->getTime();
    $reg_date->setByStamp($time);
    $record = $this->_getCounterRecord($time);

    $counters_date = new Date();
    $counters_date->setByStamp($record['time']);

    $stats_ip =& $this->getStatsIp();
    $is_new_host = $stats_ip->isNewToday($stats_request->getClientIp());

    if($counters_date->dateToDays() < $reg_date->dateToDays())
    {
      $record['hosts_today'] = 0;
      $record['hits_today'] = 0;
      $this->_insertNewDayCountersRecord($time);
    }
    elseif($counters_date->dateToDays() > $reg_date->dateToDays()) //this shouldn't normally happen
      return;

    if ($is_new_host)
    {
      $record['hosts_today']++;
      $record['hosts_all']++;
    }

    $record['hits_today']++;
    $record['hits_all']++;

    $this->_updateCountersRecord(
      $time,
      $record['hits_today'],
      $record['hosts_today'],
      $record['hits_all'],
      $record['hosts_all']);

    $this->_updateDayCountersRecord(
      $time,
      $record['hits_today'],
      $record['hosts_today'],
      $is_new_host,
      $stats_request);
  }

  function _getCounterRecord($stamp)
  {
    $record_set =& $this->counter_db_table->select();

    if(($record =& $record_set->getRow()) === false)
    {
      $record = array(
        'id' => null,
        'hosts_all' => 0,
        'hits_all' => 0,
        'hosts_today' => 0,
        'hits_today' => 0,
        'time' => $stamp
      );
      $this->counter_db_table->insert($record);

      $this->_insertNewDayCountersRecord($stamp);
    }

    return $record;
  }

  function _getNewDayCountersRecord($stamp)
  {
    $record_set =& $this->day_counters_db_table->select(array('time' => $this->makeDayStamp($stamp)));
    return $record_set->getRow();
  }

  function _insertNewDayCountersRecord($stamp)
  {
    $record = array(
      'id' => null,
      'hosts' => 0,
      'hits' => 0,
      'home_hits' => 0,
      'time' => $this->makeDayStamp($stamp)
    );
    $this->day_counters_db_table->insert($record);
  }

  function makeDayStamp($stamp)
  {
    $arr = getdate($stamp);
    return mktime(0, 0, 0, $arr['mon'], $arr['mday'], $arr['year']);
  }

  function _updateDayCountersRecord($stamp, $hits_today, $hosts_today, $is_new_host, &$stats_request)
  {
    $home_hit = $stats_request->isHomeHit() ? 1 : 0;
    $audience = ($is_new_host && $stats_request->isAudienceHit()) ? 1 : 0;

    $sql = new SimpleUpdateSQL($this->day_counters_db_table->getTableName());
    $sql->addField('hosts = ' . $hosts_today);
    $sql->addField('hits = ' . $hits_today);
    $sql->addField('home_hits = home_hits + ' . $home_hit);
    $sql->addField('audience = audience + ' . $audience);
    $sql->addCondition('time =' . $this->makeDayStamp($stamp));

    $toolkit =& Limb :: toolkit();
    $conn =& $toolkit->getDBConnection();
    $stmt =& $conn->newStatement($sql->toString());

    $stmt->execute();
  }

  function _updateCountersRecord($stamp, $hits_today, $hosts_today, $hits_all, $hosts_all)
  {
    $update_array['hits_today'] = $hits_today;
    $update_array['hosts_today'] = $hosts_today;
    $update_array['hits_all'] = $hits_all;
    $update_array['hosts_all'] = $hosts_all;
    $update_array['time'] = $stamp;

    $this->counter_db_table->update($update_array);
  }

  function setStatsIp(&$ip)
  {
    $this->stats_ip =& $ip;
  }

  function & getStatsIp()
  {
    if (is_object($this->stats_ip))
      return $this->stats_ip;

    include_once(dirname(__FILE__) . '/StatsIp.class.php');
    $this->stats_ip = new StatsIp();

    return $this->stats_ip;
  }
}
?>