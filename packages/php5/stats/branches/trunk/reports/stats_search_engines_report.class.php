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

class stats_search_engines_report
{
  protected $db = null;
  protected $filter_conditions = array();

  function __construct()
  {
    $this->db = Limb :: toolkit()->getDB();
  }

  public function fetch($params = array())
  {
    $sql = 'SELECT
            *,
            COUNT(engine) as hits
            FROM
            sys_stat_search_phrase';

    $sql .= $this->_build_filter_condition();

    $sql .= '	GROUP BY engine
              ORDER BY hits DESC';

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $this->db->sql_exec($sql, $limit, $offset);

    return $this->db->get_array();
  }

  public function fetch_count($params = array())
  {
    $sql = 'SELECT
            engine
            FROM
            sys_stat_search_phrase';

    $sql .= $this->_build_filter_condition();

    $sql .= 'GROUP BY engine';

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
