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

require_once(LIMB_DIR . '/core/lib/session/session.class.php');
require_once(LIMB_DIR . '/core/model/chat/chat_user.class.php');
require_once(LIMB_DIR . '/core/model/chat/chat_system.class.php');

start_user_session();

if(!isset($_POST['recipient_id']) || !isset($_POST['message']))
	exit;

if(!$chat_user_data = chat_user :: get_chat_user_data())
	exit();

$file = ($_FILES['file']) ? ($_FILES['file']) : null;

chat_system :: send_message($_POST['message'], 
														$chat_user_data['chat_room_id'],
														$chat_user_data['id'],
														$_POST['recipient_id'], 
														$file);
?>
<script>
	top.send_message_finished();
</script>