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
	function stats_events_list_data_source()
	{
		parent :: data_source();		
	}

	function & get_data_set(&$counter, $params=array())
	{
		$stats_report =& new stats_report();
		$counter = $stats_report->fetch_count($params);
		return new array_dataset($stats_report->fetch($params));
	}
}
?>