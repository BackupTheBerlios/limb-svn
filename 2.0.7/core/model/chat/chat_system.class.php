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
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');
require_once(LIMB_DIR . 'core/lib/locale/strings.class.php');
if(file_exists(PROJECT_DIR . '/chat/chat_setup.php'))
{
	include_once(PROJECT_DIR . '/chat/chat_setup.php');
}
else
{
	include_once(LIMB_DIR . 'core/model/chat/chat_setup.php');
}

class chat_system
{

	function chat_system()
	{
	}
	
	function enter_chat_room($chat_user_id, $nickname, $chat_room_id)
	{
		$db =& db_factory :: instance();		
		$sql = "UPDATE chat_user
						SET chat_room_id='{$chat_room_id}', deleted=0
						WHERE id='{$chat_user_id}'";
		$db->sql_exec($sql);
		
		setcookie('chat_room_id', $chat_room_id, time()+365*24*3600, '/');
		
		$message = "system_message:". 
			sprintf(strings :: get('user_enters_chat_room', 'chat'), $nickname);
		
		chat_system :: system_message($message, $chat_room_id);
				
		return true;
	}
	
	function leave_chat_room($chat_user_id, $nickname, $chat_room_id)
	{
		if($chat_room_id == 0)
			return;

		$db =& db_factory :: instance();		

		$sql = "UPDATE chat_user
						SET chat_room_id=0, deleted=1
						WHERE id='{$chat_user_id}'";

		$db->sql_exec($sql);	

		$message = "system_message:".
			sprintf(strings :: get('user_leaves_chat_room', 'chat'), $nickname);
		chat_system :: system_message($message, $chat_room_id);
		
		return true;
	}
	
	function system_message($message, $chat_room_id, $recipient_id = -1, $file = null)
	{
		chat_system :: send_message($message, $chat_room_id, -1,  $recipient_id, $file);
	}
	
	function send_message($message, $chat_room_id, $sender_id,  $recipient_id=-1, $file=null)
	{
		$db =& db_factory :: instance();		
		$time = time();
		$message = htmlspecialchars($message);
		
		$file_id = chat_system :: add_file($file, $chat_room_id, $sender_id);
		
		$sql = "INSERT INTO chat_message 
						(message, chat_room_id, time, sender_id, recipient_id, file_id)
						values
						('{$message}', '{$chat_room_id}', {$time}, '{$sender_id}', 
							'{$recipient_id}', '{$file_id}')";

		$db->sql_exec($sql);
	}

	function add_file($file, $chat_room_id, $sender_id)
	{
		if (!$file)
			return '';
		$file_id = '';
		
		if ($file['error'] == 0)
		{
			$db =& db_factory :: instance();		
			
			$file_id = md5(uniqid(""));
			$file_data = addslashes(file_get_contents($file['tmp_name']));
			
			$info = getimagesize($file['tmp_name']);
			
			$sql = "INSERT INTO chat_file
							(id, file_data, mime_type, size, width, height)
							values
							('{$file_id}', '{$file_data}', '{$file['type']}', '{$file['size']}', '{$info[0]}', '{$info[1]}')";
			
			$db->sql_exec($sql);
		}
		elseif ($file['error'] == 1 || $file['error'] == 2)
		{
			$message = 'warning_message:'. 
				sprintf(strings :: get('big_file', 'chat'), '50kB');
			
			chat_system :: system_message($message, $chat_room_id, $sender_id);
		}
			
		return $file_id;

	}

	function get_messages_for_user($recipient_id, $chat_room_id, $last_message_id = 0)
	{
		$db =& db_factory :: instance();		

		$sql = "SELECT ignorant_id FROM chat_ignores WHERE chat_user_id='{$recipient_id}'";
		
		$db->sql_exec($sql);
		$ignorant_ids = array();
		
		while($row = $db->fetch_row())
			$ignorant_ids[] = $row['ignorant_id'];

		$ignorant_condition = '';
		if (sizeof($ignorant_ids))
			$ignorant_condition = ' AND cm.sender_id NOT IN (' . implode(',', $ignorant_ids) . ')';
		
		if ($last_message_id == 0)
			$limit = "LIMIT 100";
		
		$sql = "SELECT cm.*, cu.nickname as nickname, cu.color as color,
										cf.id as file_id, cf.size as file_size,
										cf.height as image_height, cf.width as image_width,
										cf.mime_type as mime_type
						FROM chat_message cm
						LEFT JOIN chat_file cf ON cm.file_id = cf.id
						LEFT JOIN chat_user cu
						ON cu.id = cm.sender_id
						WHERE (cm.recipient_id=-1 OR
									cm.recipient_id='{$recipient_id}' OR
									cm.sender_id='{$recipient_id}') 
									AND cm.id > {$last_message_id}
									{$ignorant_condition}
									AND cm.chat_room_id='{$chat_room_id}'
						ORDER BY cm.id DESC
						{$limit}";

		$db->sql_exec($sql);

		return array_reverse($db->get_array());
	}

	function get_users_for_room($chat_room_id, $chat_user_id = '')
	{
		$db =& db_factory :: instance();		
		
		$sql = "SELECT chat_user_id
						FROM chat_ignores
						WHERE ignorant_id='{$chat_user_id}'";

		$db->sql_exec($sql);
		$ignorer_ids = array();
		
		while($row = $db->fetch_row())
			$ignorer_ids[] = $row['chat_user_id'];

		$ignorer_condition = '';
		if (sizeof($ignorer_ids))
			$ignorer_condition = ' AND id NOT IN (' . implode(',', $ignorer_ids) . ')';
			
		$sql = "SELECT * 
						FROM chat_user
						WHERE chat_room_id='{$chat_room_id}' AND deleted = 0
						{$ignorer_condition}
						ORDER BY id";

		$db->sql_exec($sql);
		$users_list = $db->get_array('id');

		if(empty($chat_user_id))
			return $users_list;
		
		$sql = "SELECT ignorant_id
						FROM chat_ignores
						WHERE chat_user_id = {$chat_user_id}";

		$db->sql_exec($sql);
		$ignorants = $db->get_array('ignorant_id');
		
		foreach($users_list as $data)
			$users_list[$data['id']]['ignored'] = $ignorants[$data['id']] ? 1 : 0;

		return $users_list;
	}
	
	function get_chat_file($file_id)
	{
		$db =& db_factory :: instance();		
		
		$sql = "SELECT *
						FROM chat_file
						WHERE id ='{$file_id}'";

		$db->sql_exec($sql);
		$file = $db->fetch_row();
		return $file;
	}
	
	function update_user_time($chat_user_id)
	{
		$db =& db_factory :: instance();		
		$time = time();
		$sql = "UPDATE chat_user SET time={$time} WHERE id='{$chat_user_id}'";
		$db->sql_exec($sql);
	}
	
	function warn_inactive_users()
	{
		$db =& db_factory :: instance();		
		$warn_time = time() - CHAT_WARN_INACTIVE_USERS_TIME;
		$sql = "SELECT * FROM chat_user WHERE time<{$warn_time}";

		$db->sql_exec($sql);
		$users_list = $db->get_array('id');
		
		foreach($users_list as $user)
		{
			$message = "system_message:".
				sprintf(strings :: get('inactive_user_message', 'chat'), $user['nickname']);
			chat_system :: system_message($message, $user['chat_room_id']);
		}
	}
	
	function mark_deleted_users()
	{
		$db =& db_factory :: instance();		
		$delete_time = time() - CHAT_DELETE_INACTIVE_USERS_TIME;
		$sql = "SELECT * FROM chat_user WHERE time<{$delete_time}";

		$db->sql_exec($sql);
		$users_list = $db->get_array('id');
		
		if(!sizeof($users_list))
			return;
		
		foreach($users_list as $user)
		{
			$inactive_users_ids[] = $user['id'] ;
			$message = "system_message:".
				sprintf(strings :: get('user_leaves_chat_room', 'chat'), $user['nickname']);
			chat_system :: system_message($message, $user['chat_room_id']);
		}
		
		$sql = "UPDATE chat_user 
						SET deleted=1 
						WHERE id IN (" . implode(', ', $inactive_users_ids) . ")";
		
		$db->sql_exec($sql);
	}
	
	function clean_database()
	{
		$db =& db_factory :: instance();		
		$delete_time = time() - CHAT_DELETE_INACTIVE_USERS_TIME;
		$sql = "SELECT file_id FROM chat_message WHERE time<{$delete_time}";

		$db->sql_exec($sql);
		$files_list = $db->get_array('file_id');
		if(count($files_list))
		{
			$sql = "DELETE FROM chat_file 
							WHERE id IN ('". implode("','", array_keys($files_list)) . "')";
			$db->sql_exec($sql);
		}

		$sql = "DELETE FROM chat_message WHERE time<{$delete_time}";
		$db->sql_exec($sql);
		
		$sql = "SELECT DISTINCT sender_id FROM chat_message WHERE time>={$delete_time}";		
		$db->sql_exec($sql);
		$active_users_list = $db->get_array('sender_id');
		
		$chat_users_condition = '';
		$chat_ignores_condition = '';
		if(count($active_users_list))
		{
			$active_users_ids = "(". implode(",", array_keys($active_users_list)) . ")";
			$chat_users_condition = " AND id NOT IN ". $active_users_ids;
			$chat_ignores_condition = " AND chat_user_id NOT IN ". $active_users_ids;
		}
		
		$sql = "DELETE FROM chat_user WHERE deleted=1 ". $chat_users_condition;
		$db->sql_exec($sql);		

		$sql = "DELETE FROM chat_ignores WHERE 1 ". $chat_ignores_condition;
		$db->sql_exec($sql);		
	}
	
	function cleanup()
	{
		chat_system :: warn_inactive_users();
		chat_system :: mark_deleted_users();
		chat_system :: clean_database();
	}

	function toggle_ignore_user($ignorer_id, $ignorer_nickname, $ignorant_id, $chat_room_id)
	{
		if($ignorer_id == $ignorant_id)
			return;

		$db =& db_factory :: instance();		
		
		$sql = "SELECT count(*) as count
						FROM chat_ignores 
						WHERE chat_user_id='{$ignorer_id}' AND ignorant_id='{$ignorant_id}'";

		$db->sql_exec($sql);
		
		$row = $db->fetch_row();
		if ($row['count'])
		{
			$sql = "DELETE FROM chat_ignores
							WHERE chat_user_id='{$ignorer_id}' AND ignorant_id='{$ignorant_id}'";
			
			$message = "system_message:". 
				sprintf(strings :: get('user_enters_chat_room', 'chat'), $ignorer_nickname);
		
		}
		else
		{
			$sql = "INSERT INTO chat_ignores 
							(chat_user_id, ignorant_id)
							VALUES
							('{$ignorer_id}', '{$ignorant_id}')";

			$message = "system_message:". 
				sprintf(strings :: get('user_leaves_chat_room', 'chat'), $ignorer_nickname);

		}

		$db->sql_exec($sql);
		chat_system :: system_message($message, $chat_room_id, $ignorant_id);
	}

	function set_user_properties($chat_user_id, $properties)
	{
		$db =& db_factory :: instance();		
		$time = time();
		$set_statement = '';
		foreach($properties as $property => $value)
			$set_statement .= ", {$property} = '{$value}'";
		
		$sql = "UPDATE chat_user 
						SET time={$time}
						{$set_statement}
						WHERE id = {$chat_user_id}";
		
		$db->sql_exec($sql);
	}
}
?>