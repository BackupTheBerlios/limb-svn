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
require_once(LIMB_DIR . 'class/core/fetcher.class.php');

class stats_counter
{
	protected $_is_new_host = false;

	protected $_hits_today;
	protected $_hosts_today;
	protected $_hits_all;
	protected $_hosts_all;

	protected $db = null;

	public function __construct()
	{
		$this->db = db_factory :: instance();
	}

	public function set_new_host($status = true)
	{
		$this->_is_new_host = $status;
	}

	public function update($reg_date)
	{
		$reg_stamp = $reg_date->get_stamp();
		$record = $this->_get_counter_record($reg_stamp);

		$counters_date = new date();
		$counters_date->set_by_stamp($record['time']);

		if($counters_date->date_to_days() < $reg_date->date_to_days())
		{
			$record['hosts_today'] = 0;
			$record['hits_today'] = 0;
			$this->_insert_new_day_counters_record($reg_stamp);
		}
		elseif($counters_date->date_to_days() > $reg_date->date_to_days()) //this shouldn't normally happen
			return;

		if ($this->_is_new_host)
		{
			$record['hosts_today']++;
			$record['hosts_all']++;
		}

		$record['hits_today']++;
		$record['hits_all']++;

		$this->_update_counters_record(
			$reg_stamp,
			$record['hits_today'],
			$record['hosts_today'],
			$record['hits_all'],
			$record['hosts_all']);

		$this->_update_day_counters_record(
			$reg_stamp,
			$record['hits_today'],
			$record['hosts_today']);
	}

	protected function _is_new_audience()
	{
		return (!isset($_SERVER['HTTP_REFERER']));
	}

	protected function _is_home_hit()
	{
    $request = LimbToolsBox :: getToolkit()->getRequest();
    
		if(!$object_data = LimbToolsBox :: getToolkit()->getFetcher()->fetch_requested_object($request))
			return false;

		return ($object_data['parent_node_id'] == 0);
	}

	protected function _get_counter_record($stamp)
	{
		$this->db->sql_select('sys_stat_counter');

		if(($record = $this->db->fetch_row()) === false)
		{
			$record = array(
				'id' => null,
				'hosts_all' => 0,
				'hits_all' => 0,
				'hosts_today' => 0,
				'hits_today' => 0,
				'time' => $stamp
			);
			$this->db->sql_insert('sys_stat_counter', $record);

			$this->_insert_new_day_counters_record($stamp);
		}

		return $record;
	}

	protected function _get_new_day_counters_record($stamp)
	{
		$this->db->sql_select('sys_stat_day_counters', '*', array('time' => $this->make_day_stamp($stamp)));
		return $this->db->fetch_row();
	}

	protected function _insert_new_day_counters_record($stamp)
	{
		$record = array(
			'id' => null,
			'hosts' => 0,
			'hits' => 0,
			'home_hits' => 0,
			'time' => $this->make_day_stamp($stamp)
		);
		$this->db->sql_insert('sys_stat_day_counters', $record);
	}

	public function make_day_stamp($stamp)
	{
		$arr = getdate($stamp);
		return mktime(0, 0, 0, $arr['mon'], $arr['mday'], $arr['year']);
	}

	protected function _update_day_counters_record($stamp, $hits_today, $hosts_today)
	{
		$home_hit = ($this->_is_home_hit()) ? 1 : 0;
		$audience = ($this->_is_new_host && $this->_is_new_audience()) ? 1 : 0;

		$sql = "UPDATE sys_stat_day_counters
						SET hosts={$hosts_today},
						hits={$hits_today},
						home_hits=home_hits+{$home_hit},
						audience=audience+{$audience}
						WHERE
						time=" . $this->make_day_stamp($stamp);

		$this->db->sql_exec($sql);
	}

	protected function _update_counters_record($stamp, $hits_today, $hosts_today, $hits_all, $hosts_all)
	{
		$update_array['hits_today'] = $hits_today;
		$update_array['hosts_today'] = $hosts_today;
		$update_array['hits_all'] = $hits_all;
		$update_array['hosts_all'] = $hosts_all;
		$update_array['time'] = $stamp;

		$this->db->sql_update('sys_stat_counter', $update_array);
	}
}
?>