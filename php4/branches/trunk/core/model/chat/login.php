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
require_once(LIMB_DIR . '/core/request/response.class.php');
require_once(LIMB_DIR . '/core/lib/system/message_box.class.php');

start_user_session();

if($_POST['nickname'] && !chat_user :: login($_POST['nickname']))
	message_box :: write_notice('such name already exists');

$response =& response :: instance();
$response->redirect($_SERVER['HTTP_REFERER']);
$response->commit();
?>