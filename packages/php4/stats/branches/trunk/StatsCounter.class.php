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
class StatsCounter
{
  var $_is_new_host = false;

  var $_hits_today;
  var $_hosts_today;
  var $_hits_all;
  var $_hosts_all;

  var $db = null;

  function StatsCounter()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& $toolkit->getDB();
  }

  function setNewHost($status = true)
  {
    $this->_is_new_host = $status;
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

  function _isHomeHit()
  {
    $toolkit =& Limb :: toolkit();
    $request =& $toolkit->getRequest();

    $datasource =& $toolkit->getDatasource('RequestedObjectDatasource');
    $datasource->setRequest($request);

    if(!$object_data = $datasource->fetch())
      return false;

    return ($object_data['parent_node_id'] == 0);
  }

  function _getCounterRecord($stamp)
  {
    $this->db->sqlSelect('sys_stat_counter');

    if(($record = $this->db->fetchRow()) === false)
    {
      $record = array(
        'id' => null,
        'hosts_all' => 0,
        'hits_all' => 0,
        'hosts_today' => 0,
        'hits_today' => 0,
        'time' => $stamp
      );
      $this->db->sqlInsert('sys_stat_counter', $record);

      $this->_insertNewDayCountersRecord($stamp);
    }

    return $record;
  }

  function _getNewDayCountersRecord($stamp)
  {
    $this->db->sqlSelect('sys_stat_day_counters', '*', array('time' => $this->makeDayStamp($stamp)));
    return $this->db->fetchRow();
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
    $this->db->sqlInsert('sys_stat_day_counters', $record);
  }

  function makeDayStamp($stamp)
  {
    $arr = getdate($stamp);
    return mktime(0, 0, 0, $arr['mon'], $arr['mday'], $arr['year']);
  }

  function _updateDayCountersRecord($stamp, $hits_today, $hosts_today)
  {
    $home_hit = ($this->_isHomeHit()) ? 1 : 0;
    $audience = ($this->_is_new_host &&  $this->_isNewAudience()) ? 1 : 0;

    $sql = "UPDATE sys_stat_day_counters
            SET hosts={$hosts_today},
            hits={$hits_today},
            home_hits=home_hits+{$home_hit},
            audience=audience+{$audience}
            WHERE
            time=" . $this->makeDayStamp($stamp);

    $this->db->sqlExec($sql);
  }

  function _updateCountersRecord($stamp, $hits_today, $hosts_today, $hits_all, $hosts_all)
  {
    $update_array['hits_today'] = $hits_today;
    $update_array['hosts_today'] = $hosts_today;
    $update_array['hits_all'] = $hits_all;
    $update_array['hosts_all'] = $hosts_all;
    $update_array['time'] = $stamp;

    $this->db->sqlUpdate('sys_stat_counter', $update_array);
  }
}
?>