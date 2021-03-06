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
		if(complex_array :: get_max_column_value('hosts', $arr, $index) !== false)
			$arr[$index]['max_hosts'] = 1;

		if(complex_array :: get_max_column_value('hits', $arr, $index) !== false)
			$arr[$index]['max_hits'] = 1;

		if(complex_array :: get_max_column_value('home_hits', $arr, $index) !== false)
			$arr[$index]['max_home_hits'] = 1;

		if(complex_array :: get_max_column_value('audience', $arr, $index) !== false)
			$arr[$index]['max_audience'] = 1;
		
		$result = array();
		foreach($arr as $index => $data)
		{
			if(date('w', $data['time']+60*60*24) == 1)
				$data['new_week'] = 1;
			
			$result[$index] = $data;
		}
			
		return $result;
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