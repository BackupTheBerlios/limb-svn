<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_events_list_data_source.class.php 38 2004-03-13 14:25:46Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . 'core/data_source/data_source.class.php');
require_once(LIMB_DIR . 'core/model/stats/stats_hits_hosts_by_days_report.class.php');

class stats_hits_hosts_list_data_source extends data_source
{		
	var $stats_hits_hosts_report = null;
	
	function stats_hits_hosts_list_data_source()
	{
		$this->stats_report =& new stats_hits_hosts_by_days_report();
		
		parent :: data_source();		
	}

	function & get_data_set(&$counter, $params=array())
	{		
		$this->_configure_filters();
		
		$counter = $this->stats_report->fetch_count($params);
		$arr = $this->stats_report->fetch($params);
		
		$arr = $this->_process_result_array($arr);
		
		return new array_dataset($arr);
	}
	
	function _process_result_array($arr)
	{
		return $arr;
	}
	
	function _configure_filters()
	{
	}
		
	/*function _set_period_filter()
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
	}*/
}
?>