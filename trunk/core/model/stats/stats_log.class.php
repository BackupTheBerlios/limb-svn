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

require_once(LIMB_DIR . '/core/lib/system/sys.class.php');
require_once(LIMB_DIR . '/core/lib/date/date.class.php');

class stats_log
{
	var $db = null;
	var $reg_date = null;
	
	function stats_log()
	{
		$this->db =& db_factory :: instance();
	}
	
	function register($node_id, $action)
	{
		$this->_reset_register_time();
		
		$referer_page_id = $this->_get_referer_page_id();
		
		$new_host = $this->_is_new_host();
		
		$this->_update_counters($new_host);
		
		$this->_update_log($node_id, $action, $referer_page_id);
	}
	
	function _reset_register_time()
	{
		$this->reg_date = new date();
	}
	
	function _is_new_host()
	{
		if(($record = $this->_get_stat_ip_record()) === false)
		{
			$this->_insert_stat_ip_record();
			return true;
		}
		
		$ip_date =& new date();
		$ip_date->set_by_stamp($record['time']);
		
		if($ip_date->is_before($this->reg_date))
		{
			$this->_update_stat_ip_record();
			return true;
		}
		elseif($ip_date->is_after($this->reg_date)) //this shouldn't happen normally...
			$this->_update_stat_ip_record();

		return false;
	}
	
	function _update_counters($new_host = false)
	{		
		if(!($record = $this->_get_counter_record()))
			return $this->_reset_all_counters();
		
		$counters_date =& new date();
		$counters_date->set_by_stamp($record['time']);
		
		if($counters_date->is_before($this->reg_date))
		{
			$this->_reset_today_counters();
			return;
		}
		
		if ($new_host)
		{
			$record['hosts_today']++;
			$record['hosts_all']++;
		}	
		
		$record['hits_today']++;
		$record['hits_all']++;
		
		$this->_update_today_counters($record['hits_today'], $record['hosts_today'], $record['hits_all'], $record['hosts_all']);	
	}
	
	function _update_log($node_id, $action, $referer_page_id)
	{
		$this->db->sql_insert('sys_stat_log', 
			array(
				'ip' => sys :: client_ip(true), 
				'time' => $this->reg_date->get_stamp(),
				'node_id' => $node_id,
				'stat_referer_id' => $referer_page_id,
				'user_id' => user :: get_id(),
				'session_id' => session_id(),
				'action' => $action,
			)
		);	
	}
	
	function _update_today_counters($hits_today, $hosts_today, $hits_all, $hosts_all)
	{
		$update_array['hits_today'] = $hits_today;
		$update_array['hosts_today'] = $hosts_today;
		$update_array['hits_all'] = $hits_all;
		$update_array['hosts_all'] = $hosts_all;
		
		$this->db->sql_update('sys_stat_counter', $update_array);
	}
	
	function _reset_today_counters()
	{
		$update_array['hits_today'] = 1;
		$update_array['hosts_today'] = 1;
		
		$this->db->sql_update('sys_stat_counter', $update_array);
	}
	
	function _reset_all_counters()
	{
		$this->db->sql_insert('sys_stat_counter', 
			array(
				'hosts_all' => 1,
				'hits_all' => 1,
				'hosts_today' => 1,
				'hits_today' => 1,
				'time' => $this->reg_date->get_stamp()
			)
		);	
	}
	
	function _get_counter_record()
	{
		$this->db->sql_select('sys_stat_counter');
		return $this->db->fetch_row();
	}
	
	function _get_referer_page_id()
	{
		if ($result = $this->_get_existing_referer_record_id())
			return $result;
		else
			return $this->_insert_referer_record();
	}
	
	function _get_clean_referer_page()
	{
		return $this->_clean_url($_SERVER['HTTP_REFERER']);
	}
	
	function _get_existing_referer_record_id()
	{
		$this->db->sql_select('sys_stat_referer_url', '*', 
			"referer_url='" . $this->_get_clean_referer_page() . "'");
		if ($referer_data = $this->db->fetch_row())
			return $referer_data['id'];
		else
			return false;	
	}
	
	function _insert_referer_record()
	{
		$this->db->sql_insert('sys_stat_referer_url', 
			array('referer_url' => $this->_get_clean_referer_page()));
		return $this->db->get_sql_insert_id('sys_stat_referer_url');		
	}
	
	function _get_stat_ip_record()
	{
		$this->db->sql_select('sys_stat_ip', '*', array('id' => sys :: client_ip(true)));
		return $this->db->fetch_row();
	}
	
	function _update_stat_ip_record()
	{
		$this->db->sql_update('sys_stat_ip', array('time' => $this->reg_date->get_stamp()), array('id' => sys :: client_ip(true)));
	}

	function _insert_stat_ip_record()
	{
		$this->db->sql_insert('sys_stat_ip', array('id' => sys :: client_ip(true), 'time' => $this->reg_date->get_stamp()));
	}
			
	function _clean_url($raw_url)
	{
		$url = trim($raw_url);
		$url = preg_replace('/(^' . preg_quote('http://' . $_SERVER['HTTP_HOST'], '/') . ')(.*)/', '\\2', $url);
		$url = preg_replace('/#[^\?]*/', '', $url);
		$url = $this->_trim_url_params($url);
		return $url;
	}
	
	function _trim_url_params($url)
	{
		if(strpos($url, '?') !== false)
		{
			$url = preg_replace('/PHPSESSID=[^&]*/', '', $url);
						
			if($pos == (strlen($url)-1))
				$url = rtrim($url, '?');
		}
		$url = rtrim($url, '/');
	}
}

?>