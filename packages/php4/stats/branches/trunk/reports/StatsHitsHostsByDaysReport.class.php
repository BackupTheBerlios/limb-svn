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
require_once(LIMB_DIR . '/class/lib/db/DbFactory.class.php');
require_once(dirname(__FILE__) . '/StatsReportInterface.interface.php');

class StatsHitsHostsByDaysReport implements StatsReportInterface
{
  protected $db;
  protected $filter_conditions = array();

  public function __construct()
  {
    $this->db = Limb :: toolkit()->getDB();
  }

  public function fetch($params = array())
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

  public function fetchCount($params = array())
  {
    $sql = "SELECT COUNT(id) as count FROM sys_stat_day_counters as ssdc";

    $sql .= $this->_buildFilterCondition();

    $this->db->sqlExec($sql);
    $arr = $this->db->fetchRow();
    return (int)$arr['count'];
  }

  public function setPeriodFilter($start_date, $finish_date)
  {
    $start_stamp = $start_date->getStamp();
    $finish_stamp = $finish_date->getStamp();

    $this->filter_conditions[] = " AND ssdc.time BETWEEN {$start_stamp} AND {$finish_stamp} ";
  }

  protected function _buildFilterCondition()
  {
    return ' WHERE 1=1 ' . implode(' ', $this->filter_conditions);
  }

  protected function _buildOrderSql($order_array)
  {
    $columns = array();

    foreach($order_array as $column => $sort_type)
      $columns[] = $column . ' ' . $sort_type;

    return implode(', ', $columns);
  }
}

?>
