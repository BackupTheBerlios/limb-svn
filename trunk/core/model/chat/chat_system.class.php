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
	require_once(PROJECT_DIR . '/chat/chat_setup.php');
}
else
{
	require_once(LIMB_DIR . 'core/model/chat/chat_setup.php');
}

class chat_system
{
	var $db;
	
	function chat_system()
	{
		$this->db =& db_factory :: instance();
	}
	
	function enter_chat_room($chat_user_id, $chat_room_id)
	{
		$sql = "SELECT * 
						FROM chat_user
						WHERE id={$chat_user_id}";

		$this->db->sql_exec($sql);
		$chat_user = $this->db->fetch_row();

		if($chat_user['chat_room_id'] > 0 && $chat_user['chat_room_id'] != $chat_room_id)
			$this->leave_chat_room($chat_user_id, $chat_user['nickname'], $chat_user['chat_room_id']);
		
		$sql = "UPDATE chat_user
						SET chat_room_id='{$chat_room_id}'
						WHERE id='{$chat_user_id}'";
		$this->db->sql_exec($sql);
		
		setcookie('chat_room_id', $chat_room_id, time()+365*24*3600, '/');
		
		$message = "system_message:". 
			sprintf(strings :: get('user_enters_chat_room', 'chat'), $chat_user['nickname']);
		
		$this->system_message($message, $chat_room_id);
				
		return true;
	}
	
	function leave_chat_room($chat_user_id, $nickname, $chat_room_id)
	{
		if($chat_room_id == 0)
			return;

		$sql = "UPDATE chat_user
						SET chat_room_id=0
						WHERE id='{$chat_user_id}'";

		$this->db->sql_exec($sql);	

		$message = "system_message:".
			sprintf(strings :: get('user_leaves_chat_room', 'chat'), $nickname);
		$this->system_message($message, $chat_room_id);
	}
	
	function system_message($message, $chat_room_id, $recipient_id = -1, $file = null)
	{
		$this->send_message($message, $chat_room_id, -1,  $recipient_id, $file);
	}
	
	function send_message($message, $chat_room_id, $sender_id,  $recipient_id=-1, $file=null)
	{
		$time = time();
		$message = htmlspecialchars($message);
		
		$file_id = $this->add_file($file, $chat_room_id, $sender_id);
		
		$sql = "INSERT INTO chat_message 
						(message, chat_room_id, time, sender_id, recipient_id, file_id)
						values
						('{$message}', '{$chat_room_id}', {$time}, '{$sender_id}', 
							'{$recipient_id}', '{$file_id}')";

		$this->db->sql_exec($sql);
	}

	function add_file($file, $chat_room_id, $sender_id)
	{
		if (!$file)
			return '';
		
		$file_id = '';
		
		if ($file['error'] == 0)
		{
			$file_id = md5(uniqid(""));
			$file_data = addslashes(file_get_contents($file['tmp_name']));
			
			$info = getimagesize($file['tmp_name']);
			
			$sql = "INSERT INTO chat_file
							(id, file_data, mime_type, size, width, height)
							values
							('{$file_id}', '{$file_data}', '{$file['type']}', '{$file['size']}', '{$info[0]}', '{$info[1]}')";
			
			$this->db->sql_exec($sql);
		}
		elseif ($file['error'] == 1 || $file['error'] == 2)
		{
			$message = 'warning_message:'. 
				sprintf(strings :: get('big_file', 'chat'), '50kB');
			
			$this->system_message($message, $chat_room_id, $sender_id);
		}
			
		return $file_id;

	}

	function get_users_for_room($chat_room_id, $chat_user_id = '')
	{
		$sql = "SELECT * 
						FROM chat_user
						WHERE chat_room_id='{$chat_room_id}' AND deleted = 0
						ORDER BY id";

		$this->db->sql_exec($sql);
		$users_list = $this->db->get_array('id');

		if(empty($chat_user_id))
			return $users_list;
		
		$sql = "SELECT ignorant_id
						FROM chat_ignores
						WHERE chat_user_id = {$chat_user_id}";

		$this->db->sql_exec($sql);
		$ignorants = $this->db->get_array('ignorant_id');
		
		foreach($users_list as $data)
			$users_list[$data['id']]['ignored'] = $ignorants[$data['id']] ? 1 : 0;

		return $users_list;
	}
	
	function get_chat_file($file_id)
	{
		$sql = "SELECT *
						FROM chat_file
						WHERE id ='{$file_id}'";

		$this->db->sql_exec($sql);
		$file = $this->db->fetch_row();
		return $file;
	}
	
	function update_user_time($chat_user_id)
	{
		$time = time();
		$sql = "UPDATE chat_user SET time={$time} WHERE id='{$chat_user_id}'";
		$this->db->sql_exec($sql);
	}
	
	function warn_inactive_users()
	{
		$warn_time = time() - CHAT_WARN_INACTIVE_USERS_TIME;
		$sql = "SELECT * FROM chat_user WHERE time<{$warn_time}";

		$this->db->sql_exec($sql);
		$users_list = $this->db->get_array('id');
		
		foreach($users_list as $user)
		{
			$message = "system_message:".
				sprintf(strings :: get('inactive_user_message', 'chat'), $user['nickname']);
			$this->system_message($message, $user['chat_room_id']);
		}
	}
	
	function mark_deleted_users()
	{
		$delete_time = time() - CHAT_DELETE_INACTIVE_USERS_TIME;
		$sql = "SELECT * FROM chat_user WHERE time<{$delete_time}";

		$this->db->sql_exec($sql);
		$users_list = $this->db->get_array('id');
		
		if(!sizeof($users_list))
			return;
		
		foreach($users_list as $user)
		{
			$inactive_users_ids[] = $user['id'] ;
			$message = "system_message:".
				sprintf(strings :: get('user_leaves_chat_room', 'chat'), $user['nickname']);
			$this->system_message($message, $user['chat_room_id']);
		}
		
		$sql = "UPDATE chat_user 
						SET deleted=1 
						WHERE id IN (" . implode(', ', $inactive_users_ids) . ")";
		
		$this->db->sql_exec($sql);
	}
	
	function clean_database()
	{
		$delete_time = time() - CHAT_DELETE_INACTIVE_USERS_TIME;
		$sql = "SELECT file_id FROM chat_message WHERE time<{$delete_time}";

		$this->db->sql_exec($sql);
		$files_list = $this->db->get_array('file_id');
		if(count($files_list))
		{
			$sql = "DELETE FROM chat_file 
							WHERE id IN ('". implode("','", array_keys($files_list)) . "')";
			$this->db->sql_exec($sql);
		}

		$sql = "DELETE FROM chat_message WHERE time<{$delete_time}";
		$this->db->sql_exec($sql);
		
		$sql = "SELECT DISTINCT sender_id FROM chat_message WHERE time>={$delete_time}";		
		$this->db->sql_exec($sql);
		$active_users_list = $this->db->get_array('sender_id');
		
		$chat_users_condition = '';
		$chat_ignores_condition = '';
		if(count($active_users_list))
		{
			$active_users_ids = "(". implode(",", array_keys($active_users_list)) . ")";
			$chat_users_condition = " AND id NOT IN ". $active_users_ids;
			$chat_ignores_condition = " AND chat_user_id NOT IN ". $active_users_ids;;
		}
		
		$sql = "DELETE FROM chat_user WHERE deleted=1 ". $chat_users_condition;
		$this->db->sql_exec($sql);		

		$sql = "DELETE FROM chat_ignores WHERE 1 ". $chat_ignores_condition;
		$this->db->sql_exec($sql);		
	}
	
	function cleanup()
	{
		$this->warn_inactive_users();
		$this->mark_deleted_users();
		$this->clean_database();
	}
}
?>