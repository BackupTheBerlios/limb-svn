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

require_once(LIMB_DIR . '/core/lib/date/date.class.php');

class stats_ip
{
	var $db = null;
	var $reg_date;
	
	function stats_ip()
	{
		$this->db =& db_factory :: instance();
		$this->reg_date = new date();		
	}

	function is_new_host()
	{
		if(($record = $this->_get_stat_ip_record()) === false)
		{
			$this->_insert_stat_ip_record();
			return true;
		}
		
		$ip_date =& new date();
		$ip_date->set_by_stamp($record['time']);
		
		if($ip_date->date_to_days() < $this->reg_date->date_to_days())
		{
			$this->_update_stat_ip_record();
			return true;
		}
		elseif($ip_date->date_to_days() > $this->reg_date->date_to_days()) //this shouldn't happen normally...
			$this->_update_stat_ip_record();

		return false;
	}

	function _insert_stat_ip_record()
	{
		$this->db->sql_insert('sys_stat_ip', 
			array(
				'id' => $this->get_client_ip(), 
				'time' => $this->get_register_time_stamp()
			)
		);
	}

	function set_register_time($stamp)
	{
		$this->reg_date->set_by_stamp($stamp);
	}

	function get_register_time_stamp()
	{
		return $this->reg_date->get_stamp();
	}

	function get_client_ip()
	{
		return sys :: client_ip(true);
	}
	
	function _get_stat_ip_record()
	{
		$this->db->sql_select('sys_stat_ip', '*', array('id' => $this->get_client_ip()));
		return $this->db->fetch_row();
	}

	function _update_stat_ip_record()
	{
		$this->db->sql_update('sys_stat_ip', 
			array('time' => $this->get_register_time_stamp()),
			array('id' => $this->get_client_ip())
		);
	}
}

?>