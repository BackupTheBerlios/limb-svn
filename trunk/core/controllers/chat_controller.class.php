<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: faculty_controller.class.php 21 2004-02-29 18:59:25Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/controllers/site_object_controller.class.php');
	
class chat_controller extends site_object_controller
{
	function chat_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'template_path' => '/chat/display.html',
				),
				'admin_display' => array(
						'permissions_required' => 'r',
						'template_path' => '/chat/admin_display.html',
						'action_name' => strings :: get('admin_display'),
				),
				'create_chat_room' => array(
						'permissions_required' => 'w',
						'template_path' => '/chat_room/create.html',
						'action_path' => '/chat_room/create_chat_room_action',
						'JIP' => true,
						'popup' => true,
						'img_src' => '/shared/images/new.generic.gif',
						'action_name' => strings :: get('create_chat_room', 'chat'),
						'can_have_access_template' => true,
				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit'),
						'action_path' => '/site_object/edit_action',
						'template_path' => '/site_object/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>