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

require_once(LIMB_DIR . 'core/datasource/datasource.class.php');
require_once(LIMB_DIR . 'core/model/stats/stats_event_report.class.php');

class stats_events_list_datasource extends datasource
{
	var $response_map = array(
				RESPONSE_STATUS_SUCCESS => 'RESPONSE_STATUS_SUCCESS', 
				RESPONSE_STATUS_FORM_DISPLAYED => 'RESPONSE_STATUS_FORM_DISPLAYED',
				RESPONSE_STATUS_FORM_SUBMITTED => 'RESPONSE_STATUS_FORM_SUBMITTED',
				RESPONSE_STATUS_FAILURE => 'RESPONSE_STATUS_FAILURE',
				RESPONSE_STATUS_FORM_NOT_VALID => 'RESPONSE_STATUS_FORM_NOT_VALID'
			);
		
	var $stats_event_report = null;
	
	function stats_events_list_datasource()
	{
		$this->stats_event_report =& new stats_event_report();
		
		parent :: datasource();		
	}

	function & get_dataset(&$counter, $params=array())
	{		
		$this->_configure_stats_event_report_filter();
		
		$counter = $this->stats_event_report->fetch_count($params);
		$arr = $this->stats_event_report->fetch($params);
		
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
	
	function _configure_stats_event_report_filter()
	{
		$this->_set_ip_filter();
		
		$this->_set_login_filter();
		
		$this->_set_action_filter();
		
		$this->_set_period_filter();
		
		$this->_set_uri_filter();
		
		$this->_set_status_filter();
	}
	
	function _set_login_filter()
	{
		if (isset($_REQUEST['stats_user_login']) && $_REQUEST['stats_user_login'])
			$this->stats_event_report->set_login_filter($_REQUEST['stats_user_login']);
	}

	function _set_action_filter()
	{
		if (isset($_REQUEST['stats_action_name']) && $_REQUEST['stats_action_name'])
			$this->stats_event_report->set_action_filter($_REQUEST['stats_action_name']);
	}
	
	function _set_ip_filter()
	{
		if (isset($_REQUEST['stats_ip']) && $_REQUEST['stats_ip'])	
			$this->stats_event_report->set_ip_filter($_REQUEST['stats_ip']);
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
			$this->stats_event_report->set_status_filter($status_mask);
	}
	
	function _set_uri_filter()
	{		
		if (isset($_REQUEST['stats_uri']) && $_REQUEST['stats_uri'])	
			$this->stats_event_report->set_uri_filter($_REQUEST['stats_uri']);
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
		
		$this->stats_event_report->set_period_filter($start_date, $finish_date);
	}
}
?>