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

class StatsReferersReport implements StatsReportInterface
{
  protected $db;
  protected $filter_conditions = array();

  public function __construct()
  {
    $this->db = Limb :: toolkit()->getDB();
  }

  public function fetch($params = array())
  {
    $sql = 'SELECT
            stat_referer_id, ssru.referer_url as referer_url,
            COUNT(stat_referer_id) as hits
            FROM
            sys_stat_log as sslog, sys_stat_referer_url as ssru';


    $sql .= $this->_buildFilterCondition();
    $sql .= ' AND sslog.stat_referer_id = ssru.id ';

    $sql .= '	GROUP BY stat_referer_id
              ORDER BY hits DESC';

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $this->db->sqlExec($sql, $limit, $offset);

    return $this->db->getArray();
  }

  public function fetchCount($params = array())
  {
    $sql = 'SELECT
            stat_referer_id
            FROM
            sys_stat_log';

    $sql .= $this->_buildFilterCondition();

    $sql .= 'GROUP BY stat_referer_id';

    $this->db->sqlExec($sql);
    return $this->db->countSelectedRows();
  }

  public function fetchTotalHits()
  {
    $sql = 'SELECT
            COUNT(id) as total
            FROM
            sys_stat_log';

    $sql .= $this->_buildFilterCondition();

    $this->db->sqlExec($sql);
    $record = $this->db->fetchRow();

    return $record['total'];
  }

  public function setPeriodFilter($start_date, $finish_date)
  {
    $start_stamp = $start_date->getStamp();
    $finish_stamp = $finish_date->getStamp();

    $this->filter_conditions[] = " AND time BETWEEN {$start_stamp} AND {$finish_stamp} ";
  }

  protected function _buildFilterCondition()
  {
    return ' WHERE stat_referer_id <> -1 ' . implode(' ', $this->filter_conditions);
  }
}

?>
