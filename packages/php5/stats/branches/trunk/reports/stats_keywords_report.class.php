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
require_once(LIMB_DIR . '/class/lib/db/db_factory.class.php');
require_once(dirname(__FILE__) . '/stats_report_interface.interface.php');

class stats_keywords_report implements stats_report_interface
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

    $sql .= $this->_build_filter_condition();

    $sql .= '	GROUP BY phrase
              ORDER BY hits DESC';

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $this->db->sql_exec($sql, $limit, $offset);

    return $this->db->get_array();
  }

  public function fetch_count($params = array())
  {
    $sql = 'SELECT
            phrase
            FROM
            sys_stat_search_phrase';

    $sql .= $this->_build_filter_condition();

    $sql .= 'GROUP BY phrase';

    $this->db->sql_exec($sql);
    return $this->db->count_selected_rows();
  }

  public function fetch_total_hits()
  {
    $sql = 'SELECT
            COUNT(id) as total
            FROM
            sys_stat_search_phrase';

    $sql .= $this->_build_filter_condition();

    $this->db->sql_exec($sql);
    $record = $this->db->fetch_row();

    return $record['total'];
  }

  public function set_period_filter($start_date, $finish_date)
  {
    $start_stamp = $start_date->get_stamp();
    $finish_stamp = $finish_date->get_stamp();

    $this->filter_conditions[] = " AND time BETWEEN {$start_stamp} AND {$finish_stamp} ";
  }

  protected function _build_filter_condition()
  {
    return ' WHERE 1=1 ' . implode(' ', $this->filter_conditions);
  }
}

?>
