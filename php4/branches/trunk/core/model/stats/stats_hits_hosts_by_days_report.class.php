<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');

class stats_hits_hosts_by_days_report
{
  var $db = null;
  var $filter_conditions = array();

  function stats_hits_hosts_by_days_report()
  {
    $this->db =& db_factory :: instance();
  }

  function fetch($params = array())
  {
    $sql = "SELECT *
            FROM
            sys_stat_day_counters as ssdc";

    $sql .= $this->_build_filter_condition();

    if(isset($params['order']))
      $sql .= ' ORDER BY ' . $this->_build_order_sql($params['order']);

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $this->db->sql_exec($sql, $limit, $offset);

    return $this->db->get_array();
  }

  function fetch_count($params = array())
  {
    $sql = "SELECT COUNT(id) as count FROM sys_stat_day_counters as ssdc";

    $sql .= $this->_build_filter_condition();

    $this->db->sql_exec($sql);
    $arr =& $this->db->fetch_row();
    return (int)$arr['count'];
  }

  function set_period_filter($start_date, $finish_date)
  {
    $start_stamp = $start_date->get_stamp();
    $finish_stamp = $finish_date->get_stamp();

    $this->filter_conditions[] = " AND ssdc.time BETWEEN {$start_stamp} AND {$finish_stamp} ";
  }

  function _build_filter_condition()
  {
    return ' WHERE 1=1 ' . implode(' ', $this->filter_conditions);
  }

  function _build_order_sql($order_array)
  {
    $columns = array();

    foreach($order_array as $column => $sort_type)
      $columns[] = $column . ' ' . $sort_type;

    return implode(', ', $columns);
  }
}

?>
