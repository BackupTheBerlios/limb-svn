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
require_once(LIMB_DIR . 'core/model/chat/chat_user.class.php');
require_once(LIMB_DIR . 'core/model/chat/chat_system.class.php');

start_user_session();

$chat_user_data = chat_user :: get_chat_user_data();
chat_system :: leave_chat_room(
	$chat_user_data['id'],
	$chat_user_data['nickname'],
	$chat_user_data['chat_room_id']
);
?>