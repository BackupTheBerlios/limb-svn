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
require_once(LIMB_DIR . 'class/datasources/datasource.interface.php');

abstract class stats_report_datasource implements datasource
{		
	protected $_stats_report;
	
	abstract protected function _init_stats_report();

	public function get_dataset(&$counter, $params=array())
	{		
		$this->_configure_filters();
		
		$counter = $this->_stats_report->fetch_count($params);
		$raw_data = $this->_stats_report->fetch($params);

		$result = $this->_process_result_array($raw_data);
		
		return new array_dataset($result);
	}

	abstract protected function _configure_filters();

	abstract protected function _process_result_array($arr);
}
?>