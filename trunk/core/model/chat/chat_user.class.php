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


class chat_user
{
	var $db;
	
	function chat_user()
	{
		$this->db =& db_factory :: instance();
	}
	
	function login($data)
	{
		if(empty($data['nickname']))
			return false;
			
		if(user :: is_logged_in())
			return $this->_login_to_chat(user :: get_login());
		
		$sql = "SELECT u.identifier 
						FROM user u, sys_site_object sso
						WHERE u.object_id = sso.id AND u.version = sso.current_version";

		$this->db->sql_exec($sql);
		$users = $this->db->get_array();
		
		if(in_array($data['nickname'], $users))
			return false;
		
		return $this->_login_to_chat($data['nickname']);
				
	}
	
	function _login_to_chat($nickname)
	{
		$time = time();
		$sql = "SELECT id, nickname FROM chat_user";
		$this->db->sql_exec($sql);
		$chat_users = $this->db->get_array('id');

		if(user :: is_logged_in())
		{
			foreach($chat_users as $data)
				if($data['nickname'] == $nickname)
				{
					session :: set('chat_user_id', $data['id']);
					$sql = "UPDATE chat_user 
									SET time={$time},
									host= '{$_SERVER['REMOTE_ADDR']}',
									deleted=0
								  WHERE id={$data['id']}";
					$this->db->sql_exec($sql);

					return $data['id'];
				}
		}
		else
			foreach($chat_users as $data)
				if($data['nickname'] == $nickname)
					return false;

		$sql = "INSERT INTO chat_user (nickname, time, host) 
					  values ('{$nickname}', {$time}, '{$_SERVER['REMOTE_ADDR']}')";
		$this->db->sql_exec($sql);
		
		$id = $this->db->get_sql_insert_id();
		
		session :: set('chat_user_id', $id);

		return true;
	}

	function logout()
	{
		if(!$id = session :: get('chat_user_id'))
			return true;
		
		$sql = "UPDATE chat_user SET deleted = 1 WHERE id = {$id}";

		$this->db->sql_exec($sql);

		session :: destroy('chat_user_id');
		return true;
	}
	
	function is_logged_in()
	{
		return (session :: get('chat_user_id')) ? true : false;
	}
	
	function get_chat_user_data()
	{
		if(!$id = session :: get('chat_user_id'))
			return false;

		$sql = "SELECT * FROM chat_user WHERE id='{$id}'";

		$this->db->sql_exec($sql);
		return $this->db->fetch_row();
	}
	
	function get_messages($last_message_id = 0)
	{
		if (!$chat_user_data = $this->get_chat_user_data())
			return false;
		
		$sql = "SELECT ignorant_id FROM chat_ignores WHERE chat_user_id='{$chat_user_data['id']}'";
		
		$this->db->sql_exec($sql);
		$ignorant_ids = array();
		
		while($row = $this->db->fetch_row())
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
									cm.recipient_id='{$chat_user_data['id']}' OR
									cm.sender_id='{$chat_user_data['id']}') 
									AND cm.id > {$last_message_id}
									{$ignorant_condition}
									AND cm.chat_room_id='{$chat_user_data['chat_room_id']}'
						ORDER BY cm.id DESC
						{$limit}";

		$this->db->sql_exec($sql);
		
		return array_reverse($this->db->get_array());
	}
	
	function toggle_ignore_user($ignorant_id)
	{
		if (!$chat_user_data = $this->get_chat_user_data())
			return false;
		
		$sql = "SELECT count(*) as count
						FROM chat_ignores 
						WHERE chat_user_id='{$chat_user_data['id']}' AND ignorant_id='{$ignorant_id}'";

		$this->db->sql_exec($sql);
		
		$row = $this->db->fetch_row();
		if ($row['count'])
		{
			$sql = "DELETE FROM chat_ignores
							WHERE chat_user_id='{$chat_user_data['id']}' AND ignorant_id='{$ignorant_id}'";
		}
		else
		{
			$sql = "INSERT INTO chat_ignores 
							(chat_user_id, ignorant_id)
							VALUES
							('{$chat_user_data['id']}', '{$ignorant_id}')";
		}

		$this->db->sql_exec($sql);
	}
}


?>