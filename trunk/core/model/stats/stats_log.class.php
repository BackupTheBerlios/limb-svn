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
require_once(LIMB_DIR . '/core/model/stats/stats_counter.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_ip.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_referer.class.php');

class stats_log
{
	var $db = null;
	var $reg_date = null;
	var $_counter = null;
	var $_ip_register = null;
	var $_referer_register = null;
	
	function stats_log()
	{
		$this->db =& db_factory :: instance();
		
		$this->_counter = new stats_counter();
		$this->reg_date = new date();		
	}
	
	function register($node_id, $action)
	{
		$this->_counter->set_register_time($this->get_register_time_stamp());
		
		$ip_register =& $this->_get_ip_register();
		$ip_register->set_register_time($this->get_register_time_stamp());

		$referer_register =& $this->_get_referer_register();
		$referer_register->set_register_time($this->get_register_time_stamp());

		$this->_update_counters();
		
		$this->_update_log($ip_register->get_client_ip(), $node_id, $action);
	}
	
	function reset_register_time($stamp = null)
	{
		if(!$stamp)
			$stamp = time();
			
		$this->reg_date->set_by_stamp($stamp);
	}
		
	function get_register_time_stamp()
	{
		return $this->reg_date->get_stamp();
	}
	
	function & _get_ip_register()
	{
		if (!$this->_ip_register)
			$this->_ip_register = new stats_ip();
		
		return $this->_ip_register;
	}

	function & _get_referer_register()
	{
		if (!$this->_referer_register)
			$this->_referer_register = new stats_referer();
		
		return $this->_referer_register;
	}
	
	function _update_counters()
	{	
		$ip_register =& $this->_get_ip_register();
		
		$this->_counter->update($ip_register->is_new_host());
	}
	
	function _update_log($ip, $node_id, $action)
	{
		$referer_register =& $this->_get_referer_register();
	
		$this->db->sql_insert('sys_stat_log', 
			array(
				'ip' => $ip, 
				'time' => $this->get_register_time_stamp(),
				'node_id' => $node_id,
				'stat_referer_id' => $referer_register->get_referer_page_id(),
				'user_id' => user :: get_id(),
				'session_id' => session_id(),
				'action' => $action,
			)
		);	
	}
}

?>