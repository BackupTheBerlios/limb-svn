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

class stats_counter
{
	var $_hits_today;
	var $_hosts_today;
	var $_hits_all;
	var $_hosts_all;
	
	var $db = null;
	
	function stats_counter()
	{
		$this->db =& db_factory :: instance();
	}
	
	function update($reg_date, $is_new_host)
	{	
		$record = $this->_get_counter_record($reg_date->get_stamp());
		
		$counters_date =& new date();
		$counters_date->set_by_stamp($record['time']);
		
		if($counters_date->date_to_days() < $reg_date->date_to_days())
		{
			$record['hosts_today'] = 0;
			$record['hits_today'] = 0;
		}	
		
		if ($is_new_host)
		{
			$record['hosts_today']++;
			$record['hosts_all']++;
		}	
		
		$record['hits_today']++;
		$record['hits_all']++;
		
		$this->_update_today_counters(
			$reg_date->get_stamp(), 
			$record['hits_today'], 
			$record['hosts_today'], 
			$record['hits_all'], 
			$record['hosts_all']);	
	}
	
	function _get_counter_record($stamp)
	{
		$this->db->sql_select('sys_stat_counter');
		
		if(($record = $this->db->fetch_row()) === false)
		{
			$record = array(
				'hosts_all' => 0,
				'hits_all' => 0,
				'hosts_today' => 0,
				'hits_today' => 0,
				'time' => $stamp
			);
			$this->db->sql_insert('sys_stat_counter', $record);
		}
		
		return $record;
	}

	function _update_today_counters($stamp, $hits_today, $hosts_today, $hits_all, $hosts_all)
	{
		$update_array['hits_today'] = $hits_today;
		$update_array['hosts_today'] = $hosts_today;
		$update_array['hits_all'] = $hits_all;
		$update_array['hosts_all'] = $hosts_all;
		$update_array['time'] = $stamp;
		
		$this->db->sql_update('sys_stat_counter', $update_array);
	}
	
}
?>