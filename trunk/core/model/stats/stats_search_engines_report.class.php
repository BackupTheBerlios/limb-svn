<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_ips_report.class.php 59 2004-03-22 13:54:41Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

class stats_search_engines_report
{
	var $connection = null;
	var $filter_conditions = array();
	
	function stats_search_engines_report()
	{
		$this->connection=& db_factory :: get_connection();
	}
		
	function fetch($params = array())
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

		$this->connection->sql_exec($sql, $limit, $offset);
		
		return $this->connection->get_array();
	}
	
	function fetch_count($params = array())
	{
		$sql = 'SELECT
						engine
						FROM 
						sys_stat_search_phrase';

		$sql .= $this->_build_filter_condition();
		
		$sql .= 'GROUP BY engine';
		
		$this->connection->sql_exec($sql);
		return $this->connection->count_selected_rows();
	}
	
	function fetch_total_hits()
	{
		$sql = 'SELECT
						COUNT(id) as total
						FROM 
						sys_stat_search_phrase';

		$sql .= $this->_build_filter_condition();
						
		$this->connection->sql_exec($sql);
		$record = $this->connection->fetch_row();
		
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
		return ' WHERE 1=1 ' . implode(' ', $this->filter_conditions);
	}
}

?>
