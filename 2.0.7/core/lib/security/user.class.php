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

define('VISITOR_USER_ID', -1);

require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

class user
{
	function user()
	{
	}
	
	function get_session_identifier()
	{
		return 'logged_in_user_data';
	}
	
	function & instance()
	{
	  $obj = null;
  	$object_name = 'global_logged_in_user_object';
  	
  	if(isset($GLOBALS[$object_name]))
			$obj =& $GLOBALS[$object_name];
		
  	if(!$obj || get_class($obj) != 'user')
  	{
  		$obj =& new user();
  		$GLOBALS[$object_name] =& $obj;
  	}  	
  	return $obj;
	}
	
	function _set_session_groups()
	{
		if (user :: is_logged_in())
			$groups_arr = user :: _get_groups();
		else
			$groups_arr = user :: _get_default_groups();
		
		if(!$groups_arr)
			return;
					
		$result = array();
		foreach($groups_arr as $group_data)
			$result[$group_data['object_id']] = $group_data['identifier'];
		
		user :: _set_session_attribute('groups', $result);
	}
	
	function _get_groups()
	{
		$db =& db_factory :: instance();
		
		$sql = 
			'SELECT sso.*, tn.*
			FROM sys_site_object as sso, user_group as tn, user_in_group as u_i_g
			WHERE sso.id=tn.object_id 
			AND sso.current_version=tn.version
			AND u_i_g.user_id='. user :: get_id() . '
			AND u_i_g.group_id=sso.id';
					
		$db->sql_exec($sql);	
		
		return $db->get_array();
	}
	
	function _get_default_groups()
	{
		$db =& db_factory :: instance();
	
		$sql = 
			'SELECT sso.*, tn.*
			FROM sys_site_object as sso, user_group as tn
			WHERE sso.identifier="visitors"
			AND sso.id=tn.object_id 
			AND sso.current_version=tn.version';
					
		$db->sql_exec($sql);	
		
		return $db->get_array();
	}
		
	function login($login, $password)
	{				
		user :: logout();
		
		if(!$record = user :: _get_identity_record($login, $password))
			return false;
			
		user :: _set_session_attribute('is_logged_in', true);			

		user :: _set_session_attribute('id', $record['id']);
		user :: _set_session_attribute('node_id', $record['node_id']);
		user :: _set_session_attribute('login', $login);
		user :: _set_session_attribute('email', $record['email']);
		user :: _set_session_attribute('name', $record['name']);
		user :: _set_session_attribute('lastname', $record['lastname']);
		user :: _set_session_attribute('password', $record['password']);
		
		user :: _set_session_groups();		

		return true;
	}
	
	function &_get_identity_record($login, $password)
	{
		$crypted_password = user :: get_crypted_password($login, $password);
		
		$db =& db_factory :: instance();
		
		$sql = 
			'SELECT *, ssot.id as node_id, sso.id as id FROM 
			sys_site_object_tree as ssot, 
			sys_site_object as sso, 
			user as tn
			WHERE sso.identifier="' . $db->escape($login) . '"
			AND tn.password="' . $db->escape($crypted_password) . '"
			AND ssot.object_id=tn.object_id
			AND sso.id=tn.object_id 
			AND sso.current_version=tn.version';
					
		$db->sql_exec($sql);
		
		return current($db->get_array());
	}
		
	function logout()
	{		
		$_SESSION[user :: get_session_identifier()] = array();		
	}
	
	function get_crypted_password($login, $none_crypt_password)
	{	
		return md5($login.$none_crypt_password);
	}
	
	function is_logged_in()
	{
		return user :: _get_session_attribute('is_logged_in', false);
	}
	
	function get_id()
	{
		return user :: _get_session_attribute('id', VISITOR_USER_ID);
	}
	
	function get_node_id()
	{
		return user :: _get_session_attribute('node_id');
	}
	
	function get_login()
	{
		return user :: _get_session_attribute('login');
	}

	function get_email()
	{
		return user :: _get_session_attribute('email');
	}

	function get_name()
	{
		return user :: _get_session_attribute('name');
	}

	function get_password()
	{
		return user :: _get_session_attribute('password');
	}

	function get_lastname()
	{
		return user :: _get_session_attribute('lastname');
	}

	function get_groups()
	{
		if(!$groups = user :: _get_session_attribute('groups', array()))
			user :: _set_session_groups();
		
		return user :: _get_session_attribute('groups', array());
	}
	
	function get_management_locale_id()
	{
		return user :: _get_session_attribute('management_locale_id', DEFAULT_MANAGEMENT_LOCALE_ID);
	}
	
	function generate_password()
	{
		$alphabet = array(
				array('b','c','d','f','g','h','g','k','l','m','n','p','q','r','s','t','v','w','x','z',
							'B','C','D','F','G','H','G','K','L','M','N','P','Q','R','S','T','V','W','X','Z'),
				array('a','e','i','o','u','y','A','E','I','O','U','Y'),
		);
		
		$new_password = '';
		for($i = 0; $i < 9 ;$i++)
		{
			$j = $i%2;
			$min_value = 0;
			$max_value = count($alphabet[$j]) - 1;
			$key = rand($min_value, $max_value);
			$new_password .= $alphabet[$j][$key];
		}
		
		return $new_password;
	}
	
	function _get_session_attribute($name, $default_value='')
	{		
		if(isset($_SESSION[user :: get_session_identifier()][$name]))
			return $_SESSION[user :: get_session_identifier()][$name];
		else
			return $default_value;
	}
	
	function _set_session_attribute($name, $value)
	{		
		$_SESSION[user :: get_session_identifier()][$name] = $value;		
	}
	
	function is_in_groups($groups_to_check)
	{
		if (!is_array($groups_to_check))
		{
			$groups_to_check = explode(',', $groups_to_check);
			if(!is_array($groups_to_check))
				return false;
		}	
			
		foreach	(user :: get_groups() as $group_name)
			if (in_array($group_name, $groups_to_check))
				return true; 
		
		return false;		
	}
	
	function _get_groups_to_check_from_string($groups)
	{
	}
}
?>