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
require_once(LIMB_DIR . '/class/lib/http/ip.class.php');
require_once(dirname(__FILE__) . '/stats_report_interface.interface.php');

class stats_event_report implements stats_report_interface
{
  protected $db;
  protected $filter_conditions = array();

  public function __construct()
  {
    $this->db = Limb :: toolkit()->getDB();
  }

  public function set_login_filter($login_string)
  {
    $condition = $this->_combine_positive_negative_conditions(
      $this->_build_positive_conditions('user.identifier', $login_string),
      $this->_build_negative_conditions('user.identifier', $login_string)
    );

    if($condition)
      $this->filter_conditions[] = ' AND ( ' . $condition . ' ) ';
  }

  public function set_action_filter($action_string)
  {
    $condition = $this->_combine_positive_negative_conditions(
      $this->_build_positive_conditions('sslog.action', $action_string),
      $this->_build_negative_conditions('sslog.action', $action_string)
    );

    if($condition)
      $this->filter_conditions[] = ' AND ( ' . $condition . ' ) ';
  }

  public function set_period_filter($start_date, $finish_date)
  {
    $start_stamp = $start_date->get_stamp();
    $finish_stamp = $finish_date->get_stamp();

    $this->filter_conditions[] = " AND sslog.time BETWEEN {$start_stamp} AND {$finish_stamp} ";
  }

  public function set_uri_filter($uri_string)
  {
    $condition = $this->_combine_positive_negative_conditions(
      $this->_build_positive_conditions('ssu.uri', $uri_string),
      $this->_build_negative_conditions('ssu.uri', $uri_string)
    );

    if($condition)
      $this->filter_conditions[] = ' AND ( ' . $condition . ' ) ';
  }

  public function set_status_filter($status_mask)
  {
    $this->filter_conditions[] = "AND (sslog.status & {$status_mask}) = sslog.status";
  }

  public function set_ip_filter($ip_string)
  {
    $ip_positive_hex_list = array();
    $ip_negative_hex_list = array();

    $ip_string_list = $this->_parse_input_string($ip_string);

    foreach($ip_string_list as $ip_piece)
    {
      if(substr($ip_piece, 0, 1) == '!')
      {
        $ip_piece = substr($ip_piece, 1);
        $ip_hex_list =& $ip_negative_hex_list;
      }
      else
        $ip_hex_list =& $ip_positive_hex_list;

      if(ip :: is_valid($ip_piece))
      {
        if(strpos($ip_piece, '*') !== false)
          $ip_hex_list[] = ip :: encode_ip(str_replace('*', '255', $ip_piece));
        else
          $ip_hex_list[] = ip :: encode_ip($ip_piece);
      }
      elseif(preg_match('/^([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})[ ]*\-[ ]*([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/', $ip_piece, $ip_match))
      {
        foreach(ip :: encode_ip_range($ip_match[1], $ip_match[2]) as $ip_range_hex_item)
          $ip_hex_list[] = $ip_range_hex_item;
      }
    }

    $positive_conditions = array();
    foreach($ip_positive_hex_list as $hex_ip)
    {
      if ( preg_match('/(ff\.)|(\.ff)/is', chunk_split($hex_ip, 2, '.')) )
        $value = str_replace('.', '', preg_replace('/(ff\.)|(\.ff)/is', '%', chunk_split($hex_ip, 2, "."))) . "'";
      else
        $value = $hex_ip;

      $positive_conditions[] = $this->_build_positive_condition('sslog.ip', $value);
    }

    $negative_conditions = array();
    foreach($ip_negative_hex_list as $hex_ip)
    {
      if ( preg_match('/(ff\.)|(\.ff)/is', chunk_split($hex_ip, 2, '.')) )
        $value = str_replace('.', '', preg_replace('/(ff\.)|(\.ff)/is', '%', chunk_split($hex_ip, 2, "."))) . "'";
      else
        $value = $hex_ip;

      $negative_conditions[] = $this->_build_negative_condition('sslog.ip', $value);
    }

    $condition = $this->_combine_positive_negative_conditions($positive_conditions, $negative_conditions);

    if($condition)
      $this->filter_conditions[] = ' AND ( ' . $condition . ' ) ';
  }

  protected function _build_filter_condition()
  {
    return ' WHERE ssu.id = sslog.stat_uri_id ' . implode(' ', $this->filter_conditions);
  }

  public function fetch($params = array())
  {
    $sql = "SELECT
            sslog.id as id,
            sslog.node_id as node_id,
            sslog.stat_referer_id as stat_referer_id,
            sslog.time as time,
            sslog.ip as ip,
            sslog.action as action,
            sslog.session_id as session_id,
            sslog.user_id as user_id,
            sslog.status as status,
            sslog.stat_uri_id as stat_uri_id,
            ssu.uri as uri,
            sso.id as object_id,
            sso.identifier as identifier,
            sso.title as title,
            user.identifier as user_login
            FROM
            sys_stat_log as sslog LEFT JOIN user ON user.object_id=sslog.user_id
            LEFT JOIN sys_site_object_tree as ssot ON ssot.id=sslog.node_id
            LEFT JOIN sys_site_object as sso ON ssot.object_id=sso.id,
            sys_stat_uri as ssu";

    $sql .= $this->_build_filter_condition();

    if(isset($params['order']))
      $sql .= ' ORDER BY ' . $this->_build_order_sql($params['order']);

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $this->db->sql_exec($sql, $limit, $offset);

    return $this->db->get_array('id');
  }

  public function fetch_count($params = array())
  {
    $sql = "SELECT COUNT(sslog.id) as count
            FROM
            sys_stat_log as sslog LEFT JOIN user ON user.object_id=sslog.user_id
            LEFT JOIN sys_site_object_tree as ssot ON ssot.id=sslog.node_id
            LEFT JOIN sys_site_object as sso ON ssot.object_id=sso.id,
            sys_stat_uri as ssu";

    $sql .= $this->_build_filter_condition();

    $this->db->sql_exec($sql);
    $arr = $this->db->fetch_row();
    return (int)$arr['count'];
  }

  protected function _build_order_sql($order_array)
  {
    $columns = array();

    foreach($order_array as $column => $sort_type)
      $columns[] = $column . ' ' . $sort_type;

    return implode(', ', $columns);
  }

  protected function _parse_input_string($input_string)
  {
    if(!$input_string = trim(str_replace('*', '%', $input_string)))
      return false;

    $items = explode(',', $input_string);
    foreach($items as $index => $item)
      $items[$index] = trim($item);

    return $items;
  }

  protected function _build_negative_conditions($field_name, $condition_string)
  {
    if(($conditions = $this->_parse_input_string($condition_string)) === false)
      return '';

    $negative_conditions = array();
    foreach($conditions as $value)
    {
      if(substr($value, 0, 1) == '!')
      {
        $value = substr($value, 1);

        $negative_conditions[] = $this->_build_negative_condition($field_name, $value);
      }
    }
    return $negative_conditions;
  }

  protected function _build_positive_conditions($field_name, $condition_string)
  {
    if(($conditions = $this->_parse_input_string($condition_string)) === false)
      return '';

    $positive_conditions = array();
    foreach($conditions as $value)
    {
      if(substr($value, 0, 1) != '!')
      {
        $positive_conditions[] = $this->_build_positive_condition($field_name, $value);
      }
    }
    return $positive_conditions;
  }

  protected function _build_negative_condition($field_name, $value)
  {
    if(strpos($value, '%') !== false)
      $negative_condition = "{$field_name} NOT LIKE '{$value}'";
    else
      $negative_condition = "{$field_name} <> '{$value}'";

    return $negative_condition;
  }

  protected function _build_positive_condition($field_name, $value)
  {
    if(strpos($value, '%') !== false)
      $negative_condition = "{$field_name} LIKE '{$value}'";
    else
      $negative_condition = "{$field_name} = '{$value}'";

    return $negative_condition;
  }

  protected function _combine_positive_negative_conditions($positive_conditions, $negative_conditions)
  {
    $sql_condition = '';

    if($positive_conditions)
      $sql_condition = '(' . implode(' OR ', $positive_conditions) . ')';

    if($negative_conditions)
    {
      if($positive_conditions)
        $sql_condition .= ' AND ';

      $sql_condition .=	'(' . implode(' AND ', $negative_conditions) . ')';
    }

    return $sql_condition;
  }
}

?>
