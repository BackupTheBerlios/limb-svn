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
require_once(LIMB_DIR . '/core/actions/action.class.php');
require_once(LIMB_DIR . '/core/model/chat/chat_user.class.php');
require_once(LIMB_DIR . '/core/model/chat/chat_system.class.php');

class display_chat_room_action extends action
{
	function perform(&$request, &$response)
	{
		$chat_room_data =& fetch_requested_object();
		
		session :: destroy('last_message_ids');
		
		setcookie('chat_room_id', $chat_room_data['object_id'], time() + 365*24*3600);
		
		if($chat_user_data = chat_user :: get_chat_user_data())
		{
			if($chat_user_data['chat_room_id'] != $chat_room_data['object_id'])
			{
				chat_system :: leave_chat_room(
					$chat_user_data['id'], 
					$chat_user_data['nickname'], 
					$chat_user_data['chat_room_id']
				);
				chat_system :: enter_chat_room(
					$chat_user_data['id'], 
					$chat_user_data['nickname'],
					$chat_room_data['object_id']
				);
			}
		
			$this->view->set('nickname', $chat_user_data['nickname']);
		}

		parent :: perform(&$request, &$response);
	}
}

?>