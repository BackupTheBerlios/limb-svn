<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_report.class.php 38 2004-03-13 14:25:46Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

class stats_hits_hosts_by_days_report
{
	var $db = null;
	var $filter_conditions = array();
	
	function stats_hits_hosts_by_days_report()
	{
		$this->db =& db_factory :: instance();
	}
		
	function fetch($params = array())
	{
		$sql = "SELECT *
						FROM
						sys_stat_day_counters as ssdc";
						
		$limit = isset($params['limit']) ? $params['limit'] : 0;
		$offset = isset($params['offset']) ? $params['offset'] : 0;

		$this->db->sql_exec($sql, $limit, $offset);
		
		return $this->db->get_array();
	}
	
	function fetch_count($params = array())
	{
		$sql = "SELECT COUNT(id) as count FROM sys_stat_day_counters";
		
		$this->db->sql_exec($sql);
		$arr =& $this->db->fetch_row();
		return (int)$arr['count'];
	}
}

?>
