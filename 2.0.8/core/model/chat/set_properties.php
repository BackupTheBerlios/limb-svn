<?php	
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: get_users.php 64 2004-03-23 16:17:13Z mike $
*
***********************************************************************************/

require_once(LIMB_DIR . 'core/lib/session/session.class.php');
require_once(LIMB_DIR . 'core/model/chat/chat_system.class.php');
require_once(LIMB_DIR . 'core/model/chat/chat_user.class.php');

start_user_session();

$chat_user_data = chat_user :: get_chat_user_data();

if ($_REQUEST['properties'] && $chat_user_data)
	chat_system :: set_user_properties(
		$chat_user_data['id'],
		$_REQUEST['properties']
		);


//$_COOKIE['chat_room_id']
$chat_room_id = $chat_user_data['chat_room_id'];
$chat_users = chat_system :: get_users_for_room($chat_room_id, $chat_user_data['id']);

?>