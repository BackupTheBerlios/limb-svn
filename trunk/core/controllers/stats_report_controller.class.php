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
	function stats_report_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/stats_report/reports_list.html',
				),
				'pages_report' => array(
						'permissions_required' => 'r',
						'template_path' => '/stats_report/pages_list.html',
						'action_name' => strings :: get('show_pages_report', 'stats'),
						'action_path' => '/stats_report/stats_pages_report_action',
						'display_in_breadcrumbs' => true
				),
				'hits_hosts_report' => array(
						'permissions_required' => 'r',
						'template_path' => '/stats_report/hits_hosts_list.html',
						'action_name' => strings :: get('show_hits_hosts_report', 'stats'),
						'action_path' => '/stats_report/stats_hits_hosts_report_action',
						'display_in_breadcrumbs' => true
				),
				'ips_report' => array(
						'permissions_required' => 'r',
						'template_path' => '/stats_report/ips_list.html',
						'action_name' => strings :: get('show_ips_report', 'stats'),
						'action_path' => '/stats_report/stats_ips_report_action',
						'display_in_breadcrumbs' => true
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>