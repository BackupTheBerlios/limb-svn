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

class stats_referers_report
{
  var $db = null;
  var $filter_conditions = array();

  function stats_referers_report()
  {
    $this->db =& db_factory :: instance();
  }

  function _get_base_sql()
  {
    return 'SELECT ' .
           'stat_referer_id, ssru.referer_url as referer_url, ' .
           'COUNT(stat_referer_id) as hits ' .
           'FROM ' .
           'sys_stat_log as sslog, sys_stat_referer_url as ssru ' .
           'WHERE %s ' .
           'AND sslog.stat_referer_id = ssru.id ' .
           'GROUP BY stat_referer_id ' .
           'ORDER BY hits DESC';
  }

  function _get_base_count_sql()
  {
    return 'SELECT ' .
           'stat_referer_id ' .
           'FROM ' .
           'sys_stat_log ' .
           'WHERE %s ' .
           'GROUP BY stat_referer_id';
  }

  function _get_total_hits_sql()
  {
    return 'SELECT ' .
           'COUNT(id) as total ' .
           'FROM ' .
           'sys_stat_log ' .
           'WHERE %s ';
  }

  function _get_joined_count_sql()
  {
    return 'SELECT ' .
           'stat_referer_id ' .
           'FROM ' .
           'sys_stat_log as sslog, sys_stat_referer_url as ssru ' .
           'WHERE sslog.stat_referer_id = ssru.id AND %s ' .
           'GROUP BY stat_referer_id';
  }

  function fetch($limit = 0, $offset = 0)
  {
    $sql = sprintf($this->_get_base_sql(),
                   $this->_build_period_condition());

    $this->db->sql_exec($sql, $limit, $offset);

    return $this->db->get_array();
  }

  function fetch_count()
  {
    $sql = sprintf($this->_get_base_count_sql(),
                   $this->_build_period_condition());

    $this->db->sql_exec($sql);
    return $this->db->count_selected_rows();
  }

  function fetch_total_hits()
  {
    $sql = sprintf($this->_get_total_hits_sql(),
                   $this->_build_period_condition());

    $this->db->sql_exec($sql);
    $record = $this->db->fetch_row();

    return $record['total'];
  }

  function set_period_filter($start_date, $finish_date)
  {
    $start_stamp = $start_date->get_stamp();
    $finish_stamp = $finish_date->get_stamp();

    $this->filter_conditions[] = " AND time BETWEEN {$start_stamp} AND {$finish_stamp} ";
  }

  function fetch_by_group($group, $limit = 0, $offset = 0)
  {
    $sql = sprintf($this->_get_base_sql(),
                   $this->_build_period_condition() . ' ' .
                   $this->_build_only_groups_condition(array($group)));

    $this->db->sql_exec($sql, $limit, $offset);
    return $this->db->get_array();
  }

  function fetch_count_by_group($group)
  {
    $sql = sprintf($this->_get_joined_count_sql(),
                   $this->_build_period_condition() . ' ' .
                   $this->_build_only_groups_condition(array($group)));

    $this->db->sql_exec($sql);
    return $this->db->count_selected_rows();
  }

  function fetch_summarized_by_groups($groups)
  {
    $sql = sprintf($this->_get_base_sql(),
                   $this->_build_period_condition() . ' ' .
                   $this->_build_only_groups_condition($groups));

    $this->db->sql_exec($sql);
    return $this->_group_result($this->fetch(), $groups);
  }

  function fetch_except_groups($groups, $limit = 0, $offset = 0)
  {
    $sql = sprintf($this->_get_base_sql(),
                   $this->_build_period_condition() . ' ' .
                   $this->_build_except_groups_condition($groups));

    $this->db->sql_exec($sql, $limit, $offset);
    return $this->db->get_array();
  }

  function fetch_count_except_groups($groups)
  {
    $sql = sprintf($this->_get_joined_count_sql(),
                   $this->_build_period_condition() . ' ' .
                   $this->_build_except_groups_condition($groups));

    $this->db->sql_exec($sql);
    return $this->db->count_selected_rows();
  }

  function _group_result($array, $groups)
  {
    $hits_by_group = array();
    $grouped = array();

    $regex_groups = $this->_prepare_regex_groups($groups);

    foreach($array as $item)
    {
      if($group = $this->_get_matching_group($item['referer_url'], $regex_groups))
      {
        if(!isset($hits_by_group[$group]))
          $hits_by_group[$group] = 0;

        $hits_by_group[$group] += $item['hits'];
      }
    }

    arsort($hits_by_group);

    foreach($hits_by_group as $group => $hits)
      $grouped[] = array('referers_group' => $group, 'hits' => $hits);

    return $grouped;
  }

  function _prepare_regex_groups($groups)
  {
    $regex_groups = array();
    foreach($groups as $group)
    {
      $regex_groups[$group] = str_replace('*', '.*', preg_quote($group));
    }
    return $regex_groups;
  }

  function _get_matching_group($url, $regex_groups)
  {
    foreach($regex_groups as $group => $regex)
    {
      if(preg_match("~$regex~", $url))
        return $group;
    }
    return false;
  }

  function _build_period_condition()
  {
    return 'stat_referer_id <> -1 ' . implode(' ', $this->filter_conditions) . ' ';
  }

  function _build_except_groups_condition($groups)
  {
    $conds = array();
    foreach($groups as $group)
    {
      $conds[] = 'AND referer_url NOT LIKE "' . str_replace('*', '%', $this->db->escape($group)) . '"';
    }
    return implode(' ', $conds);
  }

  function _build_only_groups_condition($groups)
  {
    $conds = array();
    foreach($groups as $group)
    {
      $conds[] = 'AND referer_url LIKE "' . str_replace('*', '%', $this->db->escape($group)) . '"';
    }
    return implode(' ', $conds);
  }

}

?>
