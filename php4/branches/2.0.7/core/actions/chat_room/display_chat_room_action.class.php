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
require_once(LIMB_DIR . 'core/actions/action.class.php');
require_once(LIMB_DIR . 'core/model/chat/chat_user.class.php');

class display_chat_room_action extends action
{
	function display_chat_room_action()
	{
		parent :: action();
	}
	
	function perform()
	{
		$chat_room_data =& fetch_mapped_by_url();
		
		session :: destroy('last_message_ids');
		
		setcookie('chat_room_id', $chat_room_data['object_id'], time() + 365*24*3600);

		chat_user :: enter_chat_room($chat_room_data['object_id']);

		return parent :: perform();
	}
}

?>