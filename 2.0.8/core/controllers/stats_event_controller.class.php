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


require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class stats_event_controller extends site_object_controller
{
	var $_default_action = 'events_list'; 
	
	function stats_event_controller()
	{
		$this->_actions = array(
				'events_list' => array(
						'permissions_required' => 'r',
						'template_path' => '/stats_event/events_list.html',
						'action_name' => strings :: get('show_events_list', 'stats'),
						'action_path' => '/stats_event/stats_event_filter_action',
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete'),
						'action_path' => '/site_object/delete_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>