<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: user_object.class.php 470 2004-02-18 13:04:56Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');

class user_object extends content_object
{
	function user_object()
	{
		parent :: content_object();
	}
	
	function _define_class_properties()
	{
		return array(
			'class_ordr' => 1,
			'db_table_name' => 'user',
			'can_be_parent' => 0,
			'controller_class_name' => 'user_controller',
		);
	}
	
	function get_membership($user_id)
	{
		$db_table	=& db_table_factory :: instance('user_in_group');
		$groups = $db_table->get_list('user_id='. $user_id, '', 'group_id');		
		return $groups;
	}
	
	function save_membership($user_id, $membership)
	{
		$db_table	=  & db_table_factory :: instance('user_in_group');
		$db_table->delete('user_id='. $user_id);		

		foreach($membership as $group_id => $is_set)
		{
			if (!$is_set)
				continue;
				
			$data = array();
			$data['user_id'] = (int)$user_id;
			$data['group_id'] = (int)$group_id;
			$db_table->insert($data);
		}				

		return true;
	}
	
	
	function change_password()
	{
		if(!$object_id = $this->get_attribute('id'))
		{
		  debug :: write_error('object id not set', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__); 
		  return false;
		}

		$this->set_attribute(
			'password', 
			user :: get_crypted_password(
				$this->get_attribute('identifier'), 
				$this->get_attribute('password')
			)
		);
		
		return $this->update(false);
	}


	function validate_password($password)
	{
		if(!user :: is_logged_in() || !$node_id = user :: get_node_id())
		{
		  debug :: write_error('user not logged in - node id is not set', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__); 
		  return false;
		}

		$password = user :: get_crypted_password(user :: get_login(), $password);
		
		if(user :: get_password() !== $password)
			return false;
		else 
			return true;
	}

	function change_own_password($password)
	{
		if(!$node_id = user :: get_node_id())
		{
		  debug :: write_error('user not logged in - node id is not set', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__); 
		  return false;
		}

		$data['password'] = user :: get_crypted_password(user :: get_login(),	$password);
		
		$user_db_table =& db_table_factory :: create('user');
		
		if ($user_db_table->update($data, 'identifier="'. user :: get_login() . '"'))
			return user :: login(user :: get_login(), $password);
		else
			return false;
	}

	function generate_password($email)
	{
		$user_data = $this->get_user_by_email($email);
		
		$this->import_attributes($user_data);

		$new_password = user :: generate_password();
		$this->set_attribute(
			'generated_password', 
			user :: get_crypted_password($email,$new_password)
		);
		
		$result = $this->update(false);
		if($result)
		{
			$this->send_activate_password_email($user_data, $new_password);
		}
		return $result;
	}


	function activate_password()
	{
		$email = $_REQUEST['user'];
		$password = $_REQUEST['id'];
		if(empty($email) || empty($password))
			return false;

		$user_data = $this->get_user_by_email($email);
		if(($password != $user_data['password']) || empty($user_data['generated_password']))
			return false;
		
		$this->import_attributes($user_data);
		$this->set_attribute('password', $user_data['generated_password']);
		$this->set_attribute('generated_password', '');

		return $this->update(false);
	}


	function get_user_by_email($email)
	{
		$db =& db_factory :: instance();
		
		$sql = 
			'SELECT *, scot.id as node_id, sco.id as id FROM 
			sys_site_object_tree as scot, 
			sys_site_object as sco, 
			user as tn
			WHERE tn.email="' . $db->escape($email) . '"
			AND scot.id=tn.object_id
			AND sco.id=tn.object_id 
			AND sco.current_version=tn.version';
					
		$db->sql_exec($sql);
		
		return current($db->get_array());
	
	}


	function send_activate_password_email(&$user_data, $password)
	{
		global $_SERVER;
		$http_host = $_SERVER['HTTP_HOST'];

		$filename = TEMPLATES_DIR . '/user/generated_password_mail.html';
		
		if(!file_exists($filename))
		  error('template file for password notification email not found!', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('file_name' => $filename)); 
		
		$fd = fopen ($filename, "r");
		$contents = fread ($fd, filesize ($filename));
		fclose ($fd); 		

		$contents = str_replace('%website_name%', $http_host, $contents);
		$contents = str_replace('%user_name%', $user_data['name']. ' '. $user_data['lastname'], $contents);
		$contents = str_replace('%new_password%', $password, $contents);
		$contents = str_replace('%website_href%', $http_host, $contents);
		$contents = str_replace('%website_email%', ADMINISTRATOR_EMAIL, $contents);

		$activate_href = 'http://'. $http_host. '/root/activate_password?user='. $user_data['email'] .'&id='. $user_data['password']; 
		$contents = str_replace('%activate_href%', $activate_href, $contents);
		
		include_once(LIMB_DIR. '/core/lib/mail/mime_mail.class.php');

		$mail = new mime_mail();
		$mail->set_body($contents);
		$mail->build_message();
		
		if(!$mail->send($user_data['name'] . ' ' . $user_data['lastname'], $user_data['email'], $http_host, ADMINISTRATOR_EMAIL, strings :: get('generate_password_theme', 'user')))
		{
		  debug :: write_error('error while sending password notification email', 
			  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__); 
		  return false;
		}
		else
			return true;	
	}

}

?>