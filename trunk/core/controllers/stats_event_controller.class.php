<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: files_folder_controller.class.php 21 2004-03-05 11:43:13Z server $
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
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>