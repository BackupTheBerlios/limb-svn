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
require_once(LIMB_DIR . '/core/db/LimbDbPool.class.php');

class StatsHitsHostsByDaysReport //implements StatsReportInterface
{
  var $db;
  var $filter_conditions = array();

  function StatsHitsHostsByDaysReport()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& $toolkit->getDB();
  }

  function fetch($params = array())
  {
    $sql = "SELECT *
            FROM
            sys_stat_day_counters as ssdc";

    $sql .= $this->_buildFilterCondition();

    if(isset($params['order']))
      $sql .= ' ORDER BY ' . $this->_buildOrderSql($params['order']);

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $this->db->sqlExec($sql, $limit, $offset);

    return $this->db->getArray();
  }

  function fetchCount($params = array())
  {
    $sql = "SELECT COUNT(id) as count FROM sys_stat_day_counters as ssdc";

    $sql .= $this->_buildFilterCondition();

    $this->db->sqlExec($sql);
    $arr = $this->db->fetchRow();
    return (int)$arr['count'];
  }

  function setPeriodFilter($start_date, $finish_date)
  {
    $start_stamp = $start_date->getStamp();
    $finish_stamp = $finish_date->getStamp();

    $this->filter_conditions[] = " AND ssdc.time BETWEEN {$start_stamp} AND {$finish_stamp} ";
  }

  function _buildFilterCondition()
  {
    return ' WHERE 1=1 ' . implode(' ', $this->filter_conditions);
  }

  function _buildOrderSql($order_array)
  {
    $columns = array();

    foreach($order_array as $column => $sort_type)
      $columns[] = $column . ' ' . $sort_type;

    return implode(', ', $columns);
  }
}

?>
