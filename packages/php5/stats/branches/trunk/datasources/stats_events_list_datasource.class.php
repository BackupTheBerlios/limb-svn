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
require_once(dirname(__FILE__) . '/stats_report_datasource.class.php');
require_once(dirname(__FILE__) . '/../reports/stats_event_report.class.php');

class stats_events_list_datasource extends stats_report_datasource
{
	private $response_map = array(
				request :: STATUS_SUCCESS => 'STATUS_SUCCESS', 
				request :: STATUS_FORM_DISPLAYED => 'STATUS_FORM_DISPLAYED',
				request :: STATUS_FORM_SUBMITTED => 'STATUS_FORM_SUBMITTED',
				request :: STATUS_FAILURE => 'STATUS_FAILURE',
				request :: STATUS_FORM_NOT_VALID => 'STATUS_FORM_NOT_VALID'
			);
		
	protected function _init_stats_report()
	{
		$this->_stats_report = new stats_event_report();
	}
	
	protected function _process_result_array($arr)
	{
		$result = array();
		foreach($arr as $index => $data)
		{
			$data[$this->response_map[$data['status']]] = 1;
			$result[$index] = $data;
		}
			
		return $result;
	}
	
	protected function _configure_filters()
	{
	  $request = request :: instance();
	
		$this->_set_ip_filter($request);
		
		$this->_set_login_filter($request);
		
		$this->_set_action_filter($request);
		
		$this->_set_period_filter($request);
		
		$this->_set_uri_filter($request);
		
		$this->_set_status_filter($request);
	}
	
	private function _set_login_filter($request)
	{
	  if ($stats_user_login = $request->get('stats_user_login'))
			$this->_stats_report->set_login_filter($stats_user_login);
	}

	private function _set_action_filter($request)
	{
	  if ($stats_action_name = $request->get('stats_action_name'))
			$this->_stats_report->set_action_filter($stats_action_name);
	}
	
	private function _set_ip_filter($request)
	{
	  if ($stats_ip = $request->get('stats_ip'))
			$this->_stats_report->set_ip_filter($stats_ip);
	}
	
	private function _set_status_filter($request)
	{
	  if (($stats_status = $request->get('stats_status')) || (!is_array($stats_status)))
			return ;
		
		$status_mask = 0;
		$response_keys = array_keys($this->response_map);
		foreach($stats_status as $index => $on)
			if (isset($response_keys[$index]))
				$status_mask = $status_mask | $response_keys[$index];

		if ($status_mask)
			$this->_stats_report->set_status_filter($status_mask);
	}
	
	private function _set_uri_filter($request)
	{		
	  if ($stats_uri = $request->get('stats_uri'))
			$this->_stats_report->set_uri_filter($stats_uri);
	}
	
	private function _set_period_filter($request)
	{
		$locale = locale :: instance();
		$start_date = new date();
		$start_date->set_hour(0);
		$start_date->set_minute(0);
		$start_date->set_second(0);

	  if ($stats_start_date = $request->get('stats_start_date'))
			$start_date->set_by_string($stats_start_date, $locale->get_short_date_time_format());

	  if ($stats_start_hour = $request->get('stats_start_hour'))
			$start_date->set_hour($stats_start_hour);

	  if ($stats_start_minute = $request->get('stats_start_minute'))
			$start_date->set_minute($stats_start_minute);
		
		$finish_date = new date();
	  if ($stats_finish_date = $request->get('stats_finish_date'))
			$finish_date->set_by_string($stats_finish_date, $locale->get_short_date_time_format());

		$finish_date->set_hour(23);
		$finish_date->set_minute(59);
		$finish_date->set_second(59);

	  if ($stats_finish_hour = $request->get('stats_finish_hour'))
			$finish_date->set_hour($stats_finish_hour);

	  if ($stats_finish_minute = $request->get('stats_finish_minute'))
			$finish_date->set_minute($stats_finish_minute);

		$this->_stats_report->set_period_filter($start_date, $finish_date);
	}
}
?>
