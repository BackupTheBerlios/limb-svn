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

require_once(LIMB_DIR . 'core/data_source/data_source.class.php');
require_once(LIMB_DIR . 'core/model/stats/stats_report.class.php');

class stats_events_list_data_source extends data_source
{
	var $response_map = array(
				RESPONSE_STATUS_SUCCESS => 'RESPONSE_STATUS_SUCCESS', 
				RESPONSE_STATUS_FORM_DISPLAYED => 'RESPONSE_STATUS_FORM_DISPLAYED',
				RESPONSE_STATUS_FORM_SUBMITTED => 'RESPONSE_STATUS_FORM_SUBMITTED',
				RESPONSE_STATUS_FAILURE => 'RESPONSE_STATUS_FAILURE',
				RESPONSE_STATUS_FORM_NOT_VALID => 'RESPONSE_STATUS_FORM_NOT_VALID'
			);
		
	var $stats_report = null;
	
	function stats_events_list_data_source()
	{
		$this->stats_report =& new stats_report();
		
		parent :: data_source();		
	}

	function & get_data_set(&$counter, $params=array())
	{		
		$this->_configure_stats_report_filter();
		
		$counter = $this->stats_report->fetch_count($params);
		$arr = $this->stats_report->fetch($params);
		
		$arr = $this->_process_result_array($arr);
		
		return new array_dataset($arr);
	}
	
	function _process_result_array($arr)
	{
		$result = array();
		foreach($arr as $index => $data)
		{
			$data[$this->response_map[$data['status']]] = 1;
			$result[$index] = $data;
		}
			
		return $result;
	}
	
	function _configure_stats_report_filter()
	{
		$this->_set_ip_filter();
		
		$this->_set_login_filter();
		
		$this->_set_action_filter();
		
		$this->_set_period_filter();
		
		$this->_set_object_filter();
		
		$this->_set_status_filter();
	}
	
	function _set_login_filter()
	{
		if (isset($_REQUEST['stats_user_login']))
			$this->stats_report->set_login_filter($_REQUEST['stats_user_login']);
	}

	function _set_action_filter()
	{
		if (isset($_REQUEST['stats_action_name']))
			$this->stats_report->set_action_filter($_REQUEST['stats_action_name']);
	}
	
	function _set_ip_filter()
	{
		if (!isset($_REQUEST['stats_ip']))
			return;
			
		$ip_list = ip :: process_ip_range($_REQUEST['stats_ip']);
		$this->stats_report->set_ip_filter($ip_list);
	}
	
	function _set_status_filter()
	{
		if (!isset($_REQUEST['stats_status']) || !is_array($_REQUEST['stats_status']))
			return;
		
		$status_mask = 0;
		$response_keys = array_keys($this->response_map);
		foreach($_REQUEST['stats_status'] as $index => $on)
			if (isset($response_keys[$index]))
				$status_mask = $status_mask | $response_keys[$index];

		if ($status_mask)
			$this->stats_report->set_status_filter($status_mask);
	}
	
	function _set_object_filter()
	{
		if (!isset($_REQUEST['stats_object_path']) || !$_REQUEST['stats_object_path'])
			return ;

		$tree =& limb_tree :: instance();
		if($node = $tree->get_node_by_path($_REQUEST['stats_object_path']))
			$this->stats_report->set_object_filter($node['id']);
		else
			$this->stats_report->set_object_filter(-1);
	}
	
	function _set_period_filter()
	{
		$locale =& locale :: instance();
		$start_date = new date();
		$start_date->set_hour(0);
		$start_date->set_minute(0);
		$start_date->set_second(0);

		if (isset($_REQUEST['stats_start_date']))
		{
			$start_date->set_by_string($_REQUEST['stats_start_date'], $locale->get_short_date_time_format());
		}
		
		if (isset($_REQUEST['stats_start_hour']))
			$start_date->set_hour($_REQUEST['stats_start_hour']);

		if (isset($_REQUEST['stats_start_minute']))
			$start_date->set_minute($_REQUEST['stats_start_minute']);
		
		$finish_date = new date();
		$finish_date->set_hour(23);
		$finish_date->set_minute(59);
		$finish_date->set_second(59);

		if (isset($_REQUEST['stats_finish_date']))
		{
			$finish_date->set_by_string($_REQUEST['stats_finish_date'], $locale->get_short_date_time_format());
			$finish_date->set_hour(23);
			$finish_date->set_minute(59);
			$finish_date->set_second(59);
		}
		
		if (isset($_REQUEST['stats_finish_minute']))
			$finish_date->set_hour($_REQUEST['stats_finish_hour']);

		if (isset($_REQUEST['stats_finish_minute']))
			$finish_date->set_minute($_REQUEST['stats_finish_minute']);
		
		$this->stats_report->set_period_filter($start_date, $finish_date);
	}
}
?>