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

require_once(LIMB_DIR . '/core/model/stats/stats_referer.class.php');

class stats_log
{
	var $db = null;
	var $_referer_register = null;
	
	function stats_log()
	{
		$this->db =& db_factory :: instance();
	}
	
	function update($stamp, $ip, $node_id, $action, $status_code)
	{
		$referer_register =& $this->_get_referer_register();
		
		$this->db->sql_insert('sys_stat_log', 
			array(
				'ip' => $ip, 
				'time' => $stamp,
				'node_id' => $node_id,
				'stat_referer_id' => $referer_register->get_referer_page_id(),
				'user_id' => user :: get_id(),
				'session_id' => session_id(),
				'action' => $action,
				'status' => $status_code,
			)
		);	
	}
	
	function & _get_referer_register()
	{
		if (!$this->_referer_register)
			$this->_referer_register = new stats_referer();
		
		return $this->_referer_register;
	}
}

?>