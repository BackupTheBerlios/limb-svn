<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_hits_hosts_list_datasource.class.php 100 2004-03-30 12:21:26Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . 'core/datasource/datasource.class.php');
require_once(LIMB_DIR . 'core/model/stats/stats_routes_report.class.php');

class stats_routes_list_datasource extends datasource
{		
	var $stats_routes_report = null;
	
	function stats_routes_list_datasource()
	{
		$this->stats_report =& new stats_routes_report();
		
		parent :: datasource();		
	}

	function & get_dataset(&$counter, $params=array())
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
		$this->_set_period_filter();
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
		
		$finish_date = new date();

		if (isset($_REQUEST['stats_finish_date']))
		{
			$finish_date->set_by_string($_REQUEST['stats_finish_date'], $locale->get_short_date_time_format());
		}
		
		$this->stats_report->set_period_filter($start_date, $finish_date);
	}
}
?>