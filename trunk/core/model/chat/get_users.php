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

require_once(LIMB_DIR . 'core/lib/session/session.class.php');
require_once(LIMB_DIR . 'core/model/chat/chat_system.class.php');
require_once(LIMB_DIR . 'core/model/chat/chat_user.class.php');

start_user_session();

$chat_user_data = chat_user :: get_chat_user_data();

if ($_REQUEST['ignorant_id'] && $chat_user_data)
	chat_system :: toggle_ignore_user($chat_user_data['id'], $_REQUEST['ignorant_id']);



$chat_users = chat_system :: get_users_for_room($_COOKIE['chat_room_id'], $chat_user_data['id']);


if (sizeof($chat_users))
{
	$users = '';
	foreach($chat_users as $chat_user)
	{
		$users[] = "['{$chat_user['id']}','{$chat_user['nickname']}','{$chat_user['status']}', '{$chat_user['ignored']}']";
	}
	
	$users = '[' . implode(',', $users) . ']';
	
	$header = sprintf(strings :: get('users_header', 'chat'), count($chat_users));
	
	echo "<script>\n";
	echo "top.set_active_users({$users});\n";
	echo "top.update_active_users_header('{$header}');\n";
	echo "</script>\n";
}
?>