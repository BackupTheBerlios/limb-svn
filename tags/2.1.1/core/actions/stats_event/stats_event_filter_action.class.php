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
require_once(LIMB_DIR . 'core/actions/form_action.class.php');

class stats_event_filter_action extends form_action
{
	function _define_dataspace_name()
	{
	  return 'events_filter_form';
	}
 
	function _valid_perform()
	{
		$_REQUEST['stats_ip'] = $this->dataspace->get('stats_ip');
		$_REQUEST['stats_user_login'] = $this->dataspace->get('stats_user_login');
		$_REQUEST['stats_action_name'] = $this->dataspace->get('stats_action_name');
		$_REQUEST['stats_start_date'] = $this->dataspace->get('stats_start_date');
		$_REQUEST['stats_finish_date'] = $this->dataspace->get('stats_finish_date');

		$_REQUEST['stats_start_hour'] = $this->dataspace->get('stats_start_hour');
		$_REQUEST['stats_start_minute'] = $this->dataspace->get('stats_start_minute');

		$_REQUEST['stats_finish_hour'] = $this->dataspace->get('stats_finish_hour');
		$_REQUEST['stats_finish_minute'] = $this->dataspace->get('stats_finish_minute');

		$_REQUEST['stats_uri'] = $this->dataspace->get('stats_uri');
		$_REQUEST['stats_status'] = $this->dataspace->get('stats_status');
	
		return parent :: _valid_perform();
	}
}

?>