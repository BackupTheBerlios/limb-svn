<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/lib/system/sys.class.php');
require_once(LIMB_DIR . 'class/lib/db/db_factory.class.php');

class session
{
  protected function __construct(){}
  
	static public function & get($name)
	{
		if(!isset($_SESSION[$name]))
			$_SESSION[$name] = '';
		
		return $_SESSION[$name];
	}
	
	static public function set($name, $value)
	{
		$_SESSION[$name] = $value;
	}
	
	static public function session_exists($name)
	{
		return isset($_SESSION[$name]);
	}

	static public function destroy($name)
	{
		if(isset($_SESSION[$name]))
		{
			session_unregister($name);
		  unset($_SESSION[$name]);
		}
	}
	
	static public function destroy_user_session($user_id)
	{
		db_factory :: instance()->sql_delete('sys_session', "user_id='{$user_id}'");
	}
}

function start_user_session()
{
	$has_started =& $GLOBALS['session_is_started'];
	
	if (isset($has_started) && $has_started)
		return false;
	
	_register_session_db_handlers();
	
	session_start();
		
	$has_started = true;
	return true;
}

function _register_session_db_handlers()
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

function _session_db_open()
{
  return true;
}

function _session_db_close()
{
  return true;
}

function _session_db_read($session_id)
{
  $db = db_factory :: instance();
  
	$db->sql_select('sys_session', 'session_data', "session_id='{$session_id}'");
	
  if($session_res = $db->fetch_row())
  {
  	if(preg_match_all('/"*__session_class_path";s:\d+:"([^"]+)"/', $session_res['session_data'], $matches))
  	{
  		foreach($matches as $match)
  		{
  		  if(isset($match[1]))
  			  include_once($match[1]);
  		}
  	}
  	
  	return $session_res['session_data'];
  }
  else
  	return false;
}

function _session_db_write($session_id, $value)
{
	$db = db_factory :: instance();	
	
	$db->sql_select('sys_session', 'session_id', "session_id='{$session_id}'");
	
	$session_data = array(
										  'last_activity_time' => time(),
										  'session_data' => "{$value}",
										  'user_id' => user :: instance()->get_id()
										 );
	
  if($db->fetch_row())
		$db->sql_update('sys_session', $session_data, "session_id='{$session_id}'");
  else
  {
    $session_data['session_id'] = "{$session_id}";
  	$db->sql_insert('sys_session', $session_data);
	}
}

function _session_db_destroy($session_id)
{
	db_factory :: instance()->sql_delete('sys_session', "session_id='{$session_id}'");
}

function _session_db_garbage_collector($max_life_time)
{
	if(defined('SESSION_DB_MAX_LIFE_TIME') && constant('SESSION_DB_MAX_LIFE_TIME'))
		$max_life_time = constant('SESSION_DB_MAX_LIFE_TIME');

	db_factory :: instance()->sql_delete('sys_session', "last_activity_time < ". (time() - $max_life_time));
}
?>