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
require_once(LIMB_DIR . '/core/model/stats/stats_log.class.php');

class stats_register
{
	var $_counter_register = null;
	var $_log_register = null;
	var $_ip_register = null;
	var $_reg_date;
	
	function stats_register()
	{
		$this->_reg_date = new date();		
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
		$this->_update_log($node_id, $action, $status_code);
		
		$this->_update_counters();
	}
	
	function _update_log($node_id, $action, $status_code)
	{
		$ip_register =& $this->_get_ip_register();
		$log_register =& $this->_get_log_register();
		
		$log_register->update(
			$this->get_register_time_stamp(), 
			$ip_register->get_client_ip(), 
			$node_id, 
			$action, 
			$status_code);
	}
	
	function _update_counters()
	{	
		$ip_register =& $this->_get_ip_register();
		$counter_register =& $this->_get_counter_register();
		
		$counter_register->set_new_host($ip_register->is_new_host($this->_reg_date));
		$counter_register->update($this->_reg_date);
	}
	
	function & _get_log_register()
	{
		if (!$this->_log_register)
			$this->_log_register = new stats_log();
		
		return $this->_log_register;
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
}

?>