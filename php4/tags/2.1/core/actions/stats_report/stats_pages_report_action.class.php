<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_hits_hosts_report_action.class.php 38 2004-03-13 14:25:46Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/actions/form_action.class.php');

class stats_pages_report_action extends form_action
{
	function stats_pages_report_action()
	{
		parent :: form_action('pages_form');
	}
	
	function _valid_perform()
	{
		$_REQUEST['stats_start_date'] = $this->dataspace->get('stats_start_date');
		$_REQUEST['stats_finish_date'] = $this->dataspace->get('stats_finish_date');
	
		return parent :: _valid_perform();
	}
	
}

?>