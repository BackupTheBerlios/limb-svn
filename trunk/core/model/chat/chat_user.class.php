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
require_once(LIMB_DIR . 'core/model/object.class.php');
require_once(LIMB_DIR . 'core/model/chat/chat_system.class.php');

class chat_user extends object
{

	function chat_user()
	{
		parent :: object();
	}
	
	function create()
	{
		return true;
	}
	
	function update()
	{
		return true;
	}
	
	function delete()
	{
		return true;
	}
	
	function change_password()
	{
		return true;
	}

	function change_own_password()
	{
		return true;
	}

	function generate_password()
	{
		return true;
	}
	

	function activate_password()
	{
		return true;
	}
	
	function login($data)
	{
		if(empty($data['nickname']))
			return false;
			
		if(user :: is_logged_in())
			return chat_user :: _login_to_chat(user :: get_login());
		$db =& db_factory :: instance();		
		$sql = "SELECT u.identifier 
						FROM user u, sys_site_object sso
						WHERE u.object_id = sso.id AND u.version = sso.current_version";

		$db->sql_exec($sql);
		$users = $db->get_array();
		
		if(in_array($data['nickname'], $users))
			return false;
		
		return chat_user :: _login_to_chat($data['nickname']);
				
	}
	
	function _login_to_chat($nickname)
	{
		$db =& db_factory :: instance();
		$time = time();
		$sql = "SELECT id, nickname FROM chat_user";
		$db->sql_exec($sql);
		$chat_users = $db->get_array('id');

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
					$db->sql_exec($sql);

					return $data['id'];
				}
		}
		else
			foreach($chat_users as $data)
				if($data['nickname'] == $nickname)
					return false;

		$sql = "INSERT INTO chat_user (nickname, time, host, deleted) 
					  values ('{$nickname}', {$time}, '{$_SERVER['REMOTE_ADDR']}', 0)";
		$db->sql_exec($sql);
		
		$id = $db->get_sql_insert_id();
		
		session :: set('chat_user_id', $id);

		return true;
	}

	function logout()
	{
		if (!$chat_user_data = chat_user :: get_chat_user_data())
			return true;

		
		chat_system :: leave_chat_room(
				$chat_user_data['id'],
				$chat_user_data['nickname'],
				$chat_user_data['chat_room_id']
		);
		
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

		$db =& db_factory :: instance();

		$sql = "SELECT * FROM chat_user WHERE id='{$id}'";

		$db->sql_exec($sql);
		return $db->fetch_row();
	}
	
//	function get_messages($last_message_id = 0)
//	{
//		if (!$chat_user_data = chat_user :: get_chat_user_data())
//			return false;
//		
//		return chat_system :: get_messages_for_user(
//						$chat_user_data['id'],
//						$chat_user_data['chat_room_id'],
//						$last_message_id
//					);
//	}

}


?>