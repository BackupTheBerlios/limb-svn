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
require_once(dirname(__FILE__) . '/stats_report_datasource.class.php');
require_once(dirname(__FILE__) . '/../reports/stats_pages_report.class.php');

class stats_pages_list_datasource extends stats_report_datasource
{		
	protected function _init_stats_report()
	{
		$this->_stats_report = new stats_pages_report();
	}

	protected function _process_result_array($arr)		
	{
		$total = $this->_stats_report->fetch_total_hits();
			
		$result = array();
		foreach($arr as $index => $data)
		{
			$data['percentage'] = round($data['hits'] / $total * 100, 2);
				
			$result[$index] = $data;
		}
			
		return $result;
	}		
		
	protected function _configure_filters()
	{
		$this->_set_period_filter(Limb :: toolkit()->getRequest());
	}
		
	protected function _set_period_filter($request)
	{
		$locale = Limb :: toolkit()->getLocale();
		$start_date = new date();
		$start_date->set_hour(0);
		$start_date->set_minute(0);
		$start_date->set_second(0);

	  if ($stats_start_date = $request->get('stats_start_date'))
			$start_date->set_by_locale_string($locale, $stats_start_date, $locale->get_short_date_time_format());
		
		$finish_date = new date();

	  if ($stats_finish_date = $request->get('stats_finish_date'))
			$finish_date->set_by_locale_string($locale, $stats_finish_date, $locale->get_short_date_time_format());

		$finish_date->set_hour(23);
		$finish_date->set_minute(59);
		$finish_date->set_second(59);
		
		$this->_stats_report->set_period_filter($start_date, $finish_date);
	}
}
?>