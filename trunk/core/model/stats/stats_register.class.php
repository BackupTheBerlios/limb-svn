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

require_once(LIMB_DIR . '/core/model/stats/stats_counter.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_ip.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_uri.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_referer.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_search_phrase.class.php');

if(file_exists(PROJECT_DIR . '/core/model/stats/search_engines.setup.php'))
	include(PROJECT_DIR . '/core/model/stats/search_engines.setup.php');
elseif(file_exists(LIMB_DIR . '/core/model/stats/search_engines.setup.php'))
	include(LIMB_DIR . '/core/model/stats/search_engines.setup.php');

class stats_register
{
	var $_counter_register = null;
	var $_ip_register = null;
	var $_uri_register = null;
	var $_referer_register = null;
	var $_search_phrase_register = null;
	var $_reg_date;
	var $db = null;
	
	function stats_register()
	{
		$this->_reg_date = new date();		
  	$this->db =& db_factory :: instance();
	}

	function get_register_time_stamp()
	{
		return $this->_reg_date->get_stamp();
	}

	function set_register_time($stamp = null)
	{
		if(!$stamp)
			$stamp = time();
			
		$this->_reg_date->set_by_stamp($stamp);
	}

	function register($node_id, $action, $status_code)
	{
		if($status_code === REQUEST_STATUS_DONT_TRACK)
			return;
		
		$this->_update_log($node_id, $action, $status_code);
		
		$this->_update_counters();
		
		$this->_update_search_referers();
	}
	
	function _update_log($node_id, $action, $status_code)
	{
		$ip_register =& $this->_get_ip_register();

		$referer_register =& $this->_get_referer_register();
		$uri_register =& $this->_get_uri_register();
		
		$user =& user :: instance();
		
		$this->db->sql_insert('sys_stat_log', 
			array(
				'ip' => $ip_register->get_client_ip(), 
				'time' => $this->get_register_time_stamp(),
				'node_id' => $node_id,
				'stat_referer_id' => $referer_register->get_referer_page_id(),
				'stat_uri_id' => $uri_register->get_uri_id(),
				'user_id' => $user->get_id(),
				'session_id' => session_id(),
				'action' => $action,
				'status' => $status_code,
			)
		);	
	}

	function clean_until($date)
	{
		$this->db->sql_delete('sys_stat_log', 'time < ' . $date->get_stamp());
	}
	
	function count_log_records()
	{
		$this->db->sql_exec('SELECT COUNT(id) as counter FROM sys_stat_log');
		$row = $this->db->fetch_row();
		return $row['counter'];
	}
	
	function _update_counters()
	{	
		$ip_register =& $this->_get_ip_register();
		$counter_register =& $this->_get_counter_register();
		
		$counter_register->set_new_host($ip_register->is_new_host($this->_reg_date));
		$counter_register->update($this->_reg_date);
	}
	
	function _update_search_referers()
	{	
		$phrase_register =& $this->_get_search_phrase_register();
		$phrase_register->register($this->_reg_date);
	}
	
	function & _get_ip_register()
	{
		if (!$this->_ip_register)
			$this->_ip_register = new stats_ip();
		
		return $this->_ip_register;
	}	

	function & _get_counter_register()
	{
		if (!$this->_counter_register)
			$this->_counter_register = new stats_counter();
		
		return $this->_counter_register;
	}	
	
	function & _get_referer_register()
	{
		if (!$this->_referer_register)
			$this->_referer_register = new stats_referer();
		
		return $this->_referer_register;
	}
	
	function & _get_uri_register()
	{
		if (!$this->_uri_register)
			$this->_uri_register = new stats_uri();
		
		return $this->_uri_register;
	}
	
	function & _get_search_phrase_register()
	{
		if (!$this->_search_phrase_register)
			$this->_search_phrase_register =& stats_search_phrase :: instance();
		
		return $this->_search_phrase_register;
	}
}

?>