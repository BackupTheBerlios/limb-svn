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
ob_start();

require_once('setup.php');
require_once(LIMB_DIR . '/class/lib/util/progress.class.php');

if (!isset($_GET['last_message_id'])) 
	$last_message_id = -1;
else
	$last_message_id = (int)$_GET['last_message_id'];

$js = '';

if($messages = progress :: get_messages_since($last_message_id))
{
	foreach($messages as $message)
	{		
		$time_formatted = date('H:i:s', $message['time']);
				
		$js .= "top.add_message('{$time_formatted}', '{$message['id']}', '{$message['name']}', '{$message['message']}', '{$message['status']}');\n";
	}
	
	$last_message_id = $message['id'];
	
	$js .= "top.set_last_message_id({$last_message_id});\n";
}

$js .= "setTimeout('top.retrieve_messages()', 1000);\n";

echo "<html>
<script language='javascript'>
$js
</script>
<html>";		
		
ob_end_flush();
?>