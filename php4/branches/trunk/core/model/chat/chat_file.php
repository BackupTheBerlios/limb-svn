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
require_once(LIMB_DIR . '/core/model/chat/chat_system.class.php');

start_user_session();

if(!$_REQUEST['id'])
	exit();

$file = chat_system :: get_chat_file($_REQUEST['id']);

if (!$file)
{
	header("HTTP/1.1 404 Not found");
}
else
{
	header("Pragma: public");
	header("Cache-Control: private");
	header("Date: " . date("D, d M Y H:i:s") . " GMT");
	header("Content-type: {$file['mime_type']}");
	echo $file['file_data'];
}
?>