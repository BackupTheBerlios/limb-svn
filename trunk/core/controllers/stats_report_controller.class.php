<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: stats_event_controller.class.php 37 2004-03-13 10:36:02Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class stats_report_controller extends site_object_controller
{
	var $_default_action = 'hits_hosts_report'; 
	
	function stats_report_controller()
	{
		$this->_actions = array(
				'hits_hosts_report' => array(
						'permissions_required' => 'r',
						'template_path' => '/stats_report/hits_hosts_list.html',
						'action_name' => strings :: get('show_hits_hosts_report', 'stats'),
						'action_path' => '/stats_report/stats_hits_hosts_report_action',
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>