<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/error/error.inc.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/lib/http/ip.class.php');

class stats_event_report
{
	var $db = null;
	var $filter_conditions = array();
	
	function stats_event_report()
	{
		$this->db =& db_factory :: instance();
	}
	
	function set_login_filter($login)
	{
		if($login)
			$this->filter_conditions[] = "AND user.identifier = '{$login}'";
	}

	function set_action_filter($action)
	{
		if($action)
			$this->filter_conditions[] = "AND sslog.action = '{$action}'";
	}
	
	function set_period_filter($start_date, $finish_date)
	{
		$start_stamp = $start_date->get_stamp();
		$finish_stamp = $finish_date->get_stamp();
		
		$this->filter_conditions[] = " AND sslog.time BETWEEN {$start_stamp} AND {$finish_stamp} ";
	}
	
	function set_object_filter($node_id)
	{
		$this->filter_conditions[] = " AND sslog.node_id = {$node_id}";
	}
	
	function set_status_filter($status_mask)
	{
		$this->filter_conditions[] = "AND (sslog.status & {$status_mask}) = sslog.status";
	}
	
	function set_ip_filter($ip_list)
	{		
		$filter_ip_arr = array();
		
		foreach($ip_list as $ip)
		{
			if ( preg_match('/(ff\.)|(\.ff)/is', chunk_split($ip, 2, '.')) )
				$filter_ip_arr[] = "ip LIKE '" . str_replace('.', '', preg_replace('/(ff\.)|(\.ff)/is', '%', chunk_split($ip, 2, "."))) . "'";
			else
				$filter_ip_arr[] = "ip = '" . $ip . "'";
		}
		if($filter_ip_arr)
			$this->filter_conditions[] = 'AND (' . implode(' OR ', $filter_ip_arr) . ')';
	}
		
	function _build_filter_condition()
	{
		return ' WHERE ssu.id = sslog.stat_uri_id ' . implode(' ', $this->filter_conditions);
	}
	
	function fetch($params = array())
	{
		$sql = "SELECT 
						sslog.*, ssu.uri,
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
	
	function fetch_count($params = array())
	{
		$sql = "SELECT COUNT(sslog.id) as count 
						FROM
						sys_stat_log as sslog LEFT JOIN user ON user.object_id=sslog.user_id 
						LEFT JOIN sys_site_object_tree as ssot ON ssot.id=sslog.node_id
						LEFT JOIN sys_site_object as sso ON ssot.object_id=sso.id,
						sys_stat_uri as ssu";
		
		$sql .= $this->_build_filter_condition();
		
		$this->db->sql_exec($sql);
		$arr =& $this->db->fetch_row();
		return (int)$arr['count'];
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
