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
require_once(LIMB_DIR . '/core/http/Ip.class.php');

class StatsEventReport //implements StatsReportInterface
{
  var $db;
  var $filter_conditions = array();

  function StatsEventReport()
  {
    $toolkit =& Limb :: toolkit();
    $this->db =& $toolkit->getDB();
  }

  function setLoginFilter($login_string)
  {
    $condition = $this->_combinePositiveNegativeConditions(
      $this->_buildPositiveConditions('user.identifier', $login_string),
      $this->_buildNegativeConditions('user.identifier', $login_string)
    );

    if($condition)
      $this->filter_conditions[] = ' AND ( ' . $condition . ' ) ';
  }

  function setActionFilter($action_string)
  {
    $condition = $this->_combinePositiveNegativeConditions(
      $this->_buildPositiveConditions('sslog.action', $action_string),
      $this->_buildNegativeConditions('sslog.action', $action_string)
    );

    if($condition)
      $this->filter_conditions[] = ' AND ( ' . $condition . ' ) ';
  }

  function setPeriodFilter($start_date, $finish_date)
  {
    $start_stamp = $start_date->getStamp();
    $finish_stamp = $finish_date->getStamp();

    $this->filter_conditions[] = " AND sslog.time BETWEEN {$start_stamp} AND {$finish_stamp} ";
  }

  function setUriFilter($uri_string)
  {
    $condition = $this->_combinePositiveNegativeConditions(
      $this->_buildPositiveConditions('ssu.uri', $uri_string),
      $this->_buildNegativeConditions('ssu.uri', $uri_string)
    );

    if($condition)
      $this->filter_conditions[] = ' AND ( ' . $condition . ' ) ';
  }

  function setStatusFilter($status_mask)
  {
    $this->filter_conditions[] = "AND (sslog.status & {$status_mask}) = sslog.status";
  }

  function setIpFilter($ip_string)
  {
    $ip_positive_hex_list = array();
    $ip_negative_hex_list = array();

    $ip_string_list = $this->_parseInputString($ip_string);

    foreach($ip_string_list as $ip_piece)
    {
      if(substr($ip_piece, 0, 1) == '!')
      {
        $ip_piece = substr($ip_piece, 1);
        $ip_hex_list =& $ip_negative_hex_list;
      }
      else
        $ip_hex_list =& $ip_positive_hex_list;

      if(Ip :: isValid($ip_piece))
      {
        if(strpos($ip_piece, '*') !== false)
          $ip_hex_list[] = Ip :: encodeIp(str_replace('*', '255', $ip_piece));
        else
          $ip_hex_list[] = Ip :: encodeIp($ip_piece);
      }
      elseif(preg_match('/^([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})[ ]*\-[ ]*([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/', $ip_piece, $ip_match))
      {
        foreach(Ip :: encodeIpRange($ip_match[1], $ip_match[2]) as $ip_range_hex_item)
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

      $positive_conditions[] = $this->_buildPositiveCondition('sslog.ip', $value);
    }

    $negative_conditions = array();
    foreach($ip_negative_hex_list as $hex_ip)
    {
      if ( preg_match('/(ff\.)|(\.ff)/is', chunk_split($hex_ip, 2, '.')) )
        $value = str_replace('.', '', preg_replace('/(ff\.)|(\.ff)/is', '%', chunk_split($hex_ip, 2, "."))) . "'";
      else
        $value = $hex_ip;

      $negative_conditions[] = $this->_buildNegativeCondition('sslog.ip', $value);
    }

    $condition = $this->_combinePositiveNegativeConditions($positive_conditions, $negative_conditions);

    if($condition)
      $this->filter_conditions[] = ' AND ( ' . $condition . ' ) ';
  }

  function _buildFilterCondition()
  {
    return ' WHERE ssu.id = sslog.stat_uri_id ' . implode(' ', $this->filter_conditions);
  }

  function fetch($params = array())
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

    $sql .= $this->_buildFilterCondition();

    if(isset($params['order']))
      $sql .= ' ORDER BY ' . $this->_buildOrderSql($params['order']);

    $limit = isset($params['limit']) ? $params['limit'] : 0;
    $offset = isset($params['offset']) ? $params['offset'] : 0;

    $this->db->sqlExec($sql, $limit, $offset);

    return $this->db->getArray('id');
  }

  function fetchCount($params = array())
  {
    $sql = "SELECT COUNT(sslog.id) as count
            FROM
            sys_stat_log as sslog LEFT JOIN user ON user.object_id=sslog.user_id
            LEFT JOIN sys_site_object_tree as ssot ON ssot.id=sslog.node_id
            LEFT JOIN sys_site_object as sso ON ssot.object_id=sso.id,
            sys_stat_uri as ssu";

    $sql .= $this->_buildFilterCondition();

    $this->db->sqlExec($sql);
    $arr = $this->db->fetchRow();
    return (int)$arr['count'];
  }

  function _buildOrderSql($order_array)
  {
    $columns = array();

    foreach($order_array as $column => $sort_type)
      $columns[] = $column . ' ' . $sort_type;

    return implode(', ', $columns);
  }

  function _parseInputString($input_string)
  {
    if(!$input_string = trim(str_replace('*', '%', $input_string)))
      return false;

    $items = explode(',', $input_string);
    foreach($items as $index => $item)
      $items[$index] = trim($item);

    return $items;
  }

  function _buildNegativeConditions($field_name, $condition_string)
  {
    if(($conditions = $this->_parseInputString($condition_string)) === false)
      return '';

    $negative_conditions = array();
    foreach($conditions as $value)
    {
      if(substr($value, 0, 1) == '!')
      {
        $value = substr($value, 1);

        $negative_conditions[] = $this->_buildNegativeCondition($field_name, $value);
      }
    }
    return $negative_conditions;
  }

  function _buildPositiveConditions($field_name, $condition_string)
  {
    if(($conditions = $this->_parseInputString($condition_string)) === false)
      return '';

    $positive_conditions = array();
    foreach($conditions as $value)
    {
      if(substr($value, 0, 1) != '!')
      {
        $positive_conditions[] = $this->_buildPositiveCondition($field_name, $value);
      }
    }
    return $positive_conditions;
  }

  function _buildNegativeCondition($field_name, $value)
  {
    if(strpos($value, '%') !== false)
      $negative_condition = "{$field_name} NOT LIKE '{$value}'";
    else
      $negative_condition = "{$field_name} <> '{$value}'";

    return $negative_condition;
  }

  function _buildPositiveCondition($field_name, $value)
  {
    if(strpos($value, '%') !== false)
      $negative_condition = "{$field_name} LIKE '{$value}'";
    else
      $negative_condition = "{$field_name} = '{$value}'";

    return $negative_condition;
  }

  function _combinePositiveNegativeConditions($positive_conditions, $negative_conditions)
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
