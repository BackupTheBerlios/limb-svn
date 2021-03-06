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
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');

class stats_referers_report
{
	var $db = null;
	var $filter_conditions = array();
	
	function stats_referers_report()
	{
		$this->db =& db_factory :: instance();
	}
		
	function fetch($params = array())
	{
		$sql = 'SELECT
						stat_referer_id, ssru.referer_url,
						COUNT(stat_referer_id) as hits 
						FROM 
						sys_stat_log as sslog, sys_stat_referer_url as ssru';
						

		$sql .= $this->_build_filter_condition();
		$sql .= ' AND sslog.stat_referer_id = ssru.id ';
		
		$sql .= '	GROUP BY stat_referer_id
							ORDER BY hits DESC';
						
		$limit = isset($params['limit']) ? $params['limit'] : 0;
		$offset = isset($params['offset']) ? $params['offset'] : 0;

		$this->db->sql_exec($sql, $limit, $offset);
		
		return $this->db->get_array();
	}
	
	function fetch_count($params = array())
	{
		$sql = 'SELECT
						stat_referer_id
						FROM 
						sys_stat_log';

		$sql .= $this->_build_filter_condition();
		
		$sql .= 'GROUP BY stat_referer_id';
		
		$this->db->sql_exec($sql);
		return $this->db->count_selected_rows();
	}
	
	function fetch_total_hits()
	{
		$sql = 'SELECT
						COUNT(id) as total
						FROM 
						sys_stat_log';
						
		$sql .= $this->_build_filter_condition();

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

	function _build_filter_condition()
	{
		return ' WHERE stat_referer_id <> -1 ' . implode(' ', $this->filter_conditions);
	}
}

?>
