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
require_once(LIMB_DIR . 'core/datasource/datasource.class.php');
require_once(LIMB_DIR . 'core/model/chat/chat_user.class.php');

class chat_users_list_datasource extends datasource
{
	function & get_dataset(&$counter, $params=array())
	{
		$chat_user =& new chat_user();
		$chat_user_data = $chat_user->get_chat_user_data();
		
		$chat_system =& new chat_system();
		$users_list = $chat_system->get_users_for_room($chat_user_data['chat_room_id']);
		return new array_dataset($users_list);

	}
}


?>
