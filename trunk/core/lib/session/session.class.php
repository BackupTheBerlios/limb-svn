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
require_once(LIMB_DIR . 'core/lib/system/sys.class.php');
require_once(LIMB_DIR . 'core/lib/security/user.class.php');
require_once(LIMB_DIR . 'core/lib/db/db_factory.class.php');

class session
{
	function session()
	{
	}

	function & get($name)
	{
		if(!isset($_SESSION[$name]))
			$_SESSION[$name] = '';
		
		return $_SESSION[$name];
	}
	
	function set($name, $value)
	{
		$_SESSION[$name] = $value;
	}
	
	function session_exists($name)
	{
		return isset($_SESSION[$name]);
	}

	function destroy($name)
	{
		if(isset($_SESSION[$name]))
		{
			session_unregister($name);
			unset($_SESSION[$name]);
		}
	}
	
	function destroy_user_session($user_id)
	{
		$db =& db_factory :: instance();
		
		$db->sql_delete('sys_session', "user_id='{$user_id}'");
	}
}

function start_user_session()
{
	$has_started =& $GLOBALS['session_is_started'];
	if (isset($has_started) && $has_started)
		return false;
	
	if(defined('SESSION_USE_DB') && constant('SESSION_USE_DB'))
		_register_session_db_functions();
	
	if(sys :: exec_mode() != 'cli')	
		@session_start();
		
	$has_started = true;
	return true;
}

function _register_session_db_functions()
{
	session_module_name('user');
	session_set_save_handler(
	    '_session_db_open',
	    '_session_db_close',
	    '_session_db_read',
	    '_session_db_write',
	    '_session_db_destroy',
	    '_session_db_garbage_collector' );
}

// re-implementation of PHP session management using database.
function _session_db_open()
{
  return true;
}

function _session_db_close()
{
  return true;
}

function & _session_db_read( $session_id )
{
	$db =& db_factory :: instance();

	$db->sql_select('sys_session', 'session_data', "session_id='{$session_id}'");
  $session_res = current($db->get_array());	

  if(sizeof($session_res) == 1)
    return $session_res['session_data'];
  else
   	return false;

}

function _session_db_write( $session_id, $value )
{
	$db =& db_factory :: instance();
	
	$db->sql_select('sys_session', '*', "session_id='{$session_id}'");

	 // check if session already exists
	$db->sql_select('sys_session', 'session_data', "session_id='{$session_id}'");
  $session_res = $db->get_array();	

  $user_id = user :: get_id();

  if(count($session_res) == 1)
		$res = $db->sql_update('sys_session', "last_activity_time=". time().", session_data='{$value}', user_id = {$user_id}" , "session_id='{$session_id}'");
  else
  	$res = $db->sql_insert('sys_session',
  			 										array(
  			 											'last_activity_time' => time(), 
  			 											'session_data' => "{$value}",
  			 											'user_id' => "{$user_id}", 
  			 											'session_id' => "{$session_id}"
  			 										)
  			 									);
}

function _session_db_destroy($session_id)
{
	$db =& db_factory :: instance();

	$db->sql_delete('sys_session', "session_id='{$session_id}'");
}

function _session_db_garbage_collector($max_life_time)
{
	$db =& db_factory :: instance();

	if(defined('SESSION_DB_MAX_LIFE_TIME') && constant('SESSION_DB_MAX_LIFE_TIME'))
		$max_life_time = constant('SESSION_DB_MAX_LIFE_TIME');

	$db->sql_delete('sys_session', "last_activity_time < ". (time() - $max_life_time));
}
?>