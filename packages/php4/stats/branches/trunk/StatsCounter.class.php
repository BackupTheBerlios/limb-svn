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

class StatsCounter
{
  var $_is_new_host = false;
  var $_is_home_hit = false;

  var $_hits_today;
  var $_hosts_today;
  var $_hits_all;
  var $_hosts_all;

  var $counter_db_table = null;
  var $day_counters_db_table = null;

  function StatsCounter()
  {
    $toolkit =& Limb :: toolkit();
    $this->counter_db_table =& $toolkit->createDBTable('StatCounter');
    $this->day_counters_db_table =& $toolkit->createDBTable('StatDayCounters');
  }

  function setNewHost($status = true)
  {
    $this->_is_new_host = $status;
  }

  function setHomeHit($status = true)
  {
    $this->_is_home_hit = $status;
  }

  function update($reg_date)
  {
    $reg_stamp = $reg_date->getStamp();
    $record = $this->_getCounterRecord($reg_stamp);

    $counters_date = new Date();
    $counters_date->setByStamp($record['time']);

    if($counters_date->dateToDays() < $reg_date->dateToDays())
    {
      $record['hosts_today'] = 0;
      $record['hits_today'] = 0;
      $this->_insertNewDayCountersRecord($reg_stamp);
    }
    elseif($counters_date->dateToDays() > $reg_date->dateToDays()) //this shouldn't normally happen
      return;

    if ($this->_is_new_host)
    {
      $record['hosts_today']++;
      $record['hosts_all']++;
    }

    $record['hits_today']++;
    $record['hits_all']++;

    $this->_updateCountersRecord(
      $reg_stamp,
      $record['hits_today'],
      $record['hosts_today'],
      $record['hits_all'],
      $record['hosts_all']);

    $this->_updateDayCountersRecord(
      $reg_stamp,
      $record['hits_today'],
      $record['hosts_today']);
  }

  function _isNewAudience()
  {
    return (!isset($_SERVER['HTTP_REFERER']));
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

  function _updateDayCountersRecord($stamp, $hits_today, $hosts_today)
  {
    $home_hit = $this->_is_home_hit ? 1 : 0;
    $audience = ($this->_is_new_host &&  $this->_isNewAudience()) ? 1 : 0;

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
}
?>