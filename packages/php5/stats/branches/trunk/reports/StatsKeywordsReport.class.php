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

class StatsKeywordsReport implements StatsReportInterface
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
            *,
            COUNT(phrase) as hits
            FROM
            sys_stat_search_phrase';

    $sql .= $this->_buildFilterCondition();

    $sql .= '	GROUP BY phrase
              ORDER BY hits DESC';

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $this->db->sqlExec($sql, $limit, $offset);

    return $this->db->getArray();
  }

  public function fetchCount($params = array())
  {
    $sql = 'SELECT
            phrase
            FROM
            sys_stat_search_phrase';

    $sql .= $this->_buildFilterCondition();

    $sql .= 'GROUP BY phrase';

    $this->db->sqlExec($sql);
    return $this->db->countSelectedRows();
  }

  public function fetchTotalHits()
  {
    $sql = 'SELECT
            COUNT(id) as total
            FROM
            sys_stat_search_phrase';

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
    return ' WHERE 1=1 ' . implode(' ', $this->filter_conditions);
  }
}

?>
