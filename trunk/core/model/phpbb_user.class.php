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
require_once(LIMB_DIR . 'core/model/object.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_table_factory.class.php');
require_once(LIMB_DIR . 'core/lib/session/session.class.php');
require_once(LIMB_DIR . 'core/lib/system/sys.class.php');

class phpbb_user extends object
{
	function phpbb_user()
	{
		parent :: object();
	}

	function create()
	{
		$data = $this->export_attributes();
		
		$phpbb_user_data = array();
		$phpbb_user_data['user_id'] = $data['id'];
  	$phpbb_user_data['user_active'] = 1;
  	$phpbb_user_data['username'] = $data['identifier'];
  	$phpbb_user_data['user_password'] = user :: get_crypted_password($data['identifier'], $data['password']);
  	$phpbb_user_data['user_regdate'] = time();
  	$phpbb_user_data['user_style'] = 1;
  	$phpbb_user_data['user_lang'] = 'english';
  	$phpbb_user_data['user_popup_pm'] = 1;
  	$phpbb_user_data['user_email'] = $data['email'];
  	
  	$db_table =& db_table_factory :: create('phpbb_users');
  	return $db_table->insert($phpbb_user_data);
	}
	
	function delete()
	{
		return true;		
	}
	
	function update($force_create_new_version = true)		
	{
		$data = $this->export_attributes();

		$phpbb_user_data = array();
		$phpbb_user_data['user_email'] = $data['email'];
  	$phpbb_user_data['username'] = $data['identifier'];

		$db =& db_factory :: instance();
		return $db->sql_update('phpbb_users', $phpbb_user_data, array('user_id' => $data['id']));
	}

	function change_password()
	{
		$data = $this->export_attributes();

		$phpbb_user_data = array();
		$phpbb_user_data['user_password'] = $data['password'];

		$db =& db_factory :: instance();
		return $db->sql_update('phpbb_users', $phpbb_user_data, array('user_id' => $data['id']));
	}

	function change_own_password($password)
	{
		return $this->change_password();
	}

	function generate_password($email)
	{
		$data = $this->export_attributes();

		$phpbb_user_data = array();
		$phpbb_user_data['user_newpasswd'] = $data['generated_password'];

		$db =& db_factory :: instance();
		return $db->sql_update('phpbb_users', $phpbb_user_data, array('user_id' => $data['id']));
	}
	
	function _delete_cookie()
	{
		$db =& db_factory :: instance();
		$db->sql_select("phpbb_config", '*', array('config_name' => 'cookie_name'));
		
		$row = $db->fetch_row();
		$cookiename = $row['config_value'];
		setcookie($cookiename . '_data', '');
		setcookie($cookiename . '_sid', '');		
	}

	function login($login, $password)
	{	
		$this->_delete_cookie();

		$user_ip = ip :: encode_ip(sys :: client_ip());
		$sid = md5(uniqid($user_ip));
		
		session :: set('phpbb_sid', $sid);
		
		$user =& user :: instance();
		
		$phpbb_user_data = array();
		$phpbb_user_data['session_user_id'] = $user->get_id();
		$phpbb_user_data['session_id'] = $sid;
		$phpbb_user_data['session_ip'] = $user_ip;
		$phpbb_user_data['session_logged_in'] = 1;
		$phpbb_user_data['session_start'] = time();
		$phpbb_user_data['session_time'] = time();
		
  	$db_table =& db_table_factory :: create('phpbb_sessions');
  	return $db_table->insert($phpbb_user_data);
	}
	
	function logout()
	{
		$this->_delete_cookie();
		
		session :: destroy('phpbb_sid');
		
		return true;
	}
	
	function activate_password()
	{
		$data = $this->export_attributes();

		$sql = "UPDATE phpbb_users
						SET `user_password` = `user_newpasswd`
						WHERE user_id = " . $data['id'];
		$db =& db_factory :: instance();
		return $db->sql_exec($sql);
	}
}

?>