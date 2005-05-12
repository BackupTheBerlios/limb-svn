<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/system/sys.class.php');
require_once(LIMB_DIR . '/core/lib/db/db_factory.class.php');

class session
{
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
  if (user_session_has_started())
    return false;

  if(defined('SESSION_USE_DB') && constant('SESSION_USE_DB'))
    _register_session_functions();

  if(sys :: exec_mode() != 'cli')
    @session_start();

  $GLOBALS['session_has_started'] = true;
  return true;
}

function commit_user_session()
{
  if(user_session_has_started())
    @session_write_close();

  if(defined('SESSION_USE_DB') && constant('SESSION_USE_DB'))
    _unregister_session_functions();
}

function user_session_has_started()
{
  return (isset($GLOBALS['session_has_started']) && $GLOBALS['session_has_started']);
}

function _register_session_functions()
{
  session_set_save_handler(
      '_session_open',
      '_session_close',
      '_session_read',
      '_session_write',
      '_session_destroy',
      '_session_garbage_collector');
}

function _unregister_session_functions()
{
  session_set_save_handler('', '', '', '', '', '');
}

function _session_open()
{
  return true;
}

function _session_close()
{
  return true;
}

function & _session_read($session_id)
{
  $db =& db_factory :: instance();

  $db->sql_select('sys_session', 'session_data', array('session_id' => $session_id));

  if($session_res = $db->fetch_row())
  {
    if(preg_match_all('/"__session_class_path";s:\d+:"([^"]+)"/', $session_res['session_data'], $matches))
    {
      foreach($matches[1] as $match)
      {
        include_once($match);
      }
    }

    return $session_res['session_data'];
  }
  else
    return false;
}

function _session_write($session_id, $value)
{
  $db =& db_factory :: instance();

  $user =& user :: instance();

  $user_id = $user->get_id();

  $db->sql_select('sys_session', 'session_id', array('session_id' => $session_id));

  if($db->fetch_row())
    $db->sql_update('sys_session',
                    array(
                       'last_activity_time'=> time(),
                       'session_data' => "{$value}",
                       'user_id' => "{$user_id}"),
                    array(
                       'session_id' => "{$session_id}"));
  else
    $db->sql_insert('sys_session',
                      array(
                        'last_activity_time' => time(),
                        'session_data' => "{$value}",
                        'user_id' => "{$user_id}",
                        'session_id' => "{$session_id}"));
}

function _session_destroy($session_id)
{
  $db =& db_factory :: instance();

  $db->sql_delete('sys_session', array('session_id' => $session_id));
}

function _session_garbage_collector($max_life_time)
{
  $db =& db_factory :: instance();

  if(defined('SESSION_DB_MAX_LIFE_TIME') && constant('SESSION_DB_MAX_LIFE_TIME'))
    $max_life_time = constant('SESSION_DB_MAX_LIFE_TIME');

  $db->sql_delete('sys_session', "last_activity_time < ". (time() - $max_life_time));
}
?>