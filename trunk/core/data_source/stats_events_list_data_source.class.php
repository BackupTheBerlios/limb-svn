<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: search_sub_branch_data_source.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . 'core/data_source/data_source.class.php');
require_once(LIMB_DIR . 'core/model/stats/stats_report.class.php');

class stats_events_list_data_source extends data_source
{
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
		return new array_dataset($this->stats_report->fetch($params));
	}
	
	function _configure_stats_report_filter()
	{
		if (isset($_REQUEST['stats_ip']))
			$this->stats_report->set_ip_filter($_REQUEST['stats_ip']);

		if (isset($_REQUEST['stats_user_login']))
			$this->stats_report->set_login_filter($_REQUEST['stats_user_login']);

		if (isset($_REQUEST['stats_action_name']))
			$this->stats_report->set_action_filter($_REQUEST['stats_action_name']);
		
		$this->_configure_period();
	}
	
	function _configure_period()
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