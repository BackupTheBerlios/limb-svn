<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

require_once(dirname(__FILE__) . '/stats_counter.class.php');
require_once(dirname(__FILE__) . '/stats_ip.class.php');
require_once(dirname(__FILE__) . '/stats_uri.class.php');
require_once(dirname(__FILE__) . '/stats_referer.class.php');
require_once(dirname(__FILE__) . '/stats_search_phrase.class.php');

include(dirname(__FILE__) . '/search_engines.setup.php');

class stats_register
{
	protected $_counter_register = null;
	protected $_ip_register = null;
	protected $_uri_register = null;
	protected $_referer_register = null;
	protected $_search_phrase_register = null;
	protected $_reg_date;
	protected $db = null;
	
	public function __construct()
	{
		$this->_reg_date = new date();		
  	$this->db = db_factory :: instance();
	}

	function get_register_time_stamp()
	{
		return $this->_reg_date->get_stamp();
	}

	public function set_register_time($stamp = null)
	{
		if(!$stamp)
			$stamp = time();
			
		$this->_reg_date->set_by_stamp($stamp);
	}

	public function register($node_id, $action, $status_code)
	{
		if($status_code === request :: STATUS_DONT_TRACK)
			return;
		
		$this->_update_log($node_id, $action, $status_code);
		
		$this->_update_counters();
		
		$this->_update_search_referers();
	}
	
	protected function _update_log($node_id, $action, $status_code)
	{
		$ip_register = $this->_get_ip_register();

		$referer_register = $this->_get_referer_register();
		$uri_register = $this->_get_uri_register();
		
		$this->db->sql_insert('sys_stat_log', 
			array(
				'ip' => $ip_register->get_client_ip(), 
				'time' => $this->get_register_time_stamp(),
				'node_id' => $node_id,
				'stat_referer_id' => $referer_register->get_referer_page_id(),
				'stat_uri_id' => $uri_register->get_uri_id(),
				'user_id' => user :: instance()->get_id(),
				'session_id' => session_id(),
				'action' => $action,
				'status' => $status_code,
			)
		);	
	}

	public function clean_until($date)
	{
		$this->db->sql_delete('sys_stat_log', 'time < ' . $date->get_stamp());
	}
	
	public function count_log_records()
	{
		$this->db->sql_exec('SELECT COUNT(id) as counter FROM sys_stat_log');
		$row = $this->db->fetch_row();
		return $row['counter'];
	}
	
	protected function _update_counters()
	{	
		$ip_register = $this->_get_ip_register();
		$counter_register = $this->_get_counter_register();
		
		$counter_register->set_new_host($ip_register->is_new_host($this->_reg_date));
		$counter_register->update($this->_reg_date);
	}
	
	protected function _update_search_referers()
	{	
		$phrase_register = $this->_get_search_phrase_register();
		$phrase_register->register($this->_reg_date);
	}
	
	protected function _get_ip_register()
	{
		if (!$this->_ip_register)
			$this->_ip_register = new stats_ip();
		
		return $this->_ip_register;
	}	

	protected function _get_counter_register()
	{
		if (!$this->_counter_register)
			$this->_counter_register = new stats_counter();
		
		return $this->_counter_register;
	}	
	
	protected function _get_referer_register()
	{
		if (!$this->_referer_register)
			$this->_referer_register = new stats_referer();
		
		return $this->_referer_register;
	}
	
	protected function _get_uri_register()
	{
		if (!$this->_uri_register)
			$this->_uri_register = new stats_uri();
		
		return $this->_uri_register;
	}
	
	protected function _get_search_phrase_register()
	{
		if (!$this->_search_phrase_register)
			$this->_search_phrase_register = stats_search_phrase :: instance();
		
		return $this->_search_phrase_register;
	}
}

?>