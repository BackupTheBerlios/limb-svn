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

class stats_ip
{
	var $db = null;
	
	function stats_ip()
	{
		$this->db =& db_factory :: instance();
	}

	function is_new_host($reg_date)
	{
		if(($record = $this->_get_stat_ip_record()) === false)
		{
			$this->_insert_stat_ip_record($reg_date->get_stamp());
			return true;
		}
		
		$ip_date =& new date();
		$ip_date->set_by_stamp($record['time']);
		
		if($ip_date->date_to_days() < $reg_date->date_to_days())
		{
			$this->_update_stat_ip_record($reg_date->get_stamp());
			return true;
		}
		elseif($ip_date->date_to_days() > $reg_date->date_to_days()) //this shouldn't happen normally...
			$this->_update_stat_ip_record($reg_date->get_stamp());

		return false;
	}

	function _insert_stat_ip_record($stamp)
	{
		$this->db->sql_insert('sys_stat_ip', 
			array(
				'id' => $this->get_client_ip(), 
				'time' => $stamp
			)
		);
	}

	function get_client_ip()
	{
		return ip :: encode_ip(sys :: client_ip());
	}
	
	function _get_stat_ip_record()
	{
		$this->db->sql_select('sys_stat_ip', '*', array('id' => $this->get_client_ip()));
		return $this->db->fetch_row();
	}

	function _update_stat_ip_record($stamp)
	{
		$this->db->sql_update('sys_stat_ip', 
			array('time' => $stamp),
			array('id' => $this->get_client_ip())
		);
	}
}

?>