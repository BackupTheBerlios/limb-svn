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
require_once(LIMB_DIR . '/core/model/stats/stats_referer.class.php');

class stats_log extends stats_supertype
{
	var $db = null;
	var $reg_date = null;
	var $_referer_register = null;
	
	function stats_log()
	{
		parent :: stats_supertype();
	}

	function set_register_time($stamp = null)
	{
		parent :: set_register_time($stamp);
		
		$referer_register =& $this->_get_referer_register();
		$referer_register->set_register_time($stamp);
	}
	
	function update($ip, $node_id, $action)
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
	
	function & _get_referer_register()
	{
		if (!$this->_referer_register)
			$this->_referer_register = new stats_referer();
		
		return $this->_referer_register;
	}
}

?>