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
	
class chat_room_controller extends site_object_controller
{
	function chat_room_controller()
	{
		$this->_actions = array(
				'display' => array(
						'permissions_required' => 'r',
						'action_path' => '/chat_room/display_chat_room_action',
						'template_path' => '/chat_room/display.html',
				),
				'edit' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('edit'),
						'action_path' => '/chat_room/edit_chat_room_action',
						'template_path' => '/chat_room/edit.html',
						'img_src' => '/shared/images/edit.gif'
				),
				'publish' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('publish'),
						'action_path' => '/doc_flow_object/publish_action',
						'img_src' => '/shared/images/publish.gif',
						'can_have_access_template' => true,
				),
				'unpublish' => array(
						'permissions_required' => 'w',
						'popup' => true,
						'JIP' => true,
						'action_name' => strings :: get('unpublish'),
						'action_path' => '/doc_flow_object/unpublish_action',
						'img_src' => '/shared/images/unpublish.gif',
						'can_have_access_template' => true,
				),
				'delete' => array(
						'permissions_required' => 'w',
						'JIP' => true,
						'popup' => true,
						'action_name' => strings :: get('delete_chat_room', 'chat'),
						'action_path' => '/chat_room/delete_chat_room_action',
						'template_path' => '/site_object/delete.html',
						'img_src' => '/shared/images/rem.gif'
				),
		);
 		
		parent :: site_object_controller();
	}
}

?>