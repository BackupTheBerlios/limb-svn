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

require_once(LIMB_DIR . '/core/model/stats/stats_supertype.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_counter.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_ip.class.php');
require_once(LIMB_DIR . '/core/model/stats/stats_log.class.php');

class stats_register extends stats_supertype
{
	var $_counter = null;
	var $_stats_log = null;
	var $_ip_register = null;
	
	function stats_register()
	{
		parent :: stats_supertype();

		$this->_counter = new stats_counter();
	}

	function set_register_time($stamp = null)
	{
		parent :: set_register_time($stamp);

		$this->_counter->set_register_time($this->get_register_time_stamp());

		$ip_register =& $this->_get_ip_register();
		$ip_register->set_register_time($stamp);

		$log_register =& $this->_get_log_register();
		$log_register->set_register_time($stamp);
	}

	function register($node_id, $action)
	{
		$this->_update_log($node_id, $action);
		
		$this->_update_counters();
	}
	
	function _update_log($node_id, $action)
	{
		$ip_register =& $this->_get_ip_register();
		$log_register =& $this->_get_log_register();
		$result = $log_register->update($ip_register->get_client_ip(), $node_id, $action);
	}
	
	function _update_counters()
	{	
		$ip_register =& $this->_get_ip_register();
		$this->_counter->update($ip_register->is_new_host());
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
}

?>