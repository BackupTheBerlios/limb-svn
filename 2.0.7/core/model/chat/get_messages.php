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
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
require_once(LIMB_DIR . 'core/lib/locale/locale.class.php');
require_once(LIMB_DIR . 'core/lib/date/date.class.php');
require_once(LIMB_DIR . 'core/lib/util/mime_type.class.php');

require_once(LIMB_DIR . 'core/model/chat/chat_user.class.php');
require_once(LIMB_DIR . 'core/model/chat/chat_system.class.php');
require_once(LIMB_DIR . 'core/model/chat/smiles.class.php');

start_user_session();

$smiles =& new smiles();

$chat_user_data = chat_user :: get_chat_user_data();
chat_system  :: update_user_time($chat_user_data['id']);

$last_message_ids = session :: get("last_message_ids");

if(!$last_message_id = $last_message_ids[$chat_user_data['chat_room_id']])
	$last_message_id = 0;

$messages = chat_system :: get_messages_for_user(
	$chat_user_data['id'],
	$chat_user_data['chat_room_id'],
	$last_message_id
);

$date =& new date();
$locale =& locale :: instance($this->locale_type);
$format_string = $locale->get_short_date_time_format();
		
echo "<html>
			<script>";

foreach($messages as $message)
{
	$message_text = $smiles->decode_smiles($message['message']);
	$message_text = str_replace("\r", "", $message_text);
	$message_text = str_replace("\n", "<br>", $message_text);
	$message_color = $message['color'];
	$sender_name = $message['nickname'];
	
	$date->set_by_stamp($message['time']);
	$message_date =		$date->format($format_string); 

	switch (true)
	{
		case $message['sender_id'] == -1:
			list($message_type, $string) = explode(':', $message['message'], 2);
			if ($message_type == 'system_message')
			{
				echo "top.add_system_message('{$message_date}', {$message['id']}, '{$string}');\n";
				echo "top.chat_user_event();";
			}
			elseif ($message_type == 'warning_message')
			{
				$string = '<img src="/shared/images/error.gif" align="center">' . $string;
				echo "top.add_warning_message('{$message_date}', {$message['id']}, '{$string}');\n";
			}
		break;

		case ($message['recipient_id'] == $chat_user_data['id']):
			$file_data = get_message_file_data($message);
			echo "top.add_private_incoming_message('{$message_date}', {$message['id']}, '{$sender_name}', \"{$message_text}\", '{$message_color}' {$file_data});\n";
		break;

		case ($message['sender_id'] == $chat_user_data['id']) && ($message['recipient_id'] > 0): 
			$file_data = get_message_file_data($message);
			echo "top.add_private_outgoing_message('{$message_date}', {$message['id']}, '{$sender_name}', \"{$message_text}\", '{$message_color}' {$file_data});\n";
		break;
		default:
			$file_data = get_message_file_data($message);
			echo "top.add_common_message('{$message_date}', {$message['id']}, '{$sender_name}', \"{$message_text}\",'{$message_color}' {$file_data});\n";

	}	
	
	$last_message_id = $message['id'];
}

if(sizeof($messages))
{
	$last_message_ids[$chat_user_data['chat_room_id']] = $last_message_id;
	session :: set("last_message_ids", $last_message_ids);
	echo "top.fetch_finished('{$last_message_id}');";
}

echo "</script>";

function get_message_file_data($message)
{
	$file_data = '';
	if ($message['file_id'])
	{
		$icon = mime_type :: get_type_icon($message['mime_type']);
		$file_data = ", ['{$message['file_id']}', '{$icon}', '{$message['file_size']}', '{$message['image_width']}', '{$message['image_height']}']";
	}

	return $file_data;
}
?>