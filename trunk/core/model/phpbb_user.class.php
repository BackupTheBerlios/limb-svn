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
require_once(LIMB_DIR . 'core/model/object.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_table_factory.class.php');

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
		$phpbb_user_data['user_id'] = $data['object_id'];
  	$phpbb_user_data['user_active'] = 1;
  	$phpbb_user_data['username'] = $data['identifier'];
  	$phpbb_user_data['user_password'] = user :: get_crypted_password($data['identifier'], $data['password']);
  	$phpbb_user_data['user_regdate'] = '';
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
		return true;		
	}

	function change_password()
	{
		return true;
	}

	function change_own_password($password)
	{
		return true;
	}

	function generate_password($email)
	{
		return true;
	}

	function activate_password()
	{
		return true;
	}
	
}

?>