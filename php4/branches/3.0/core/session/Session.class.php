<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class Session// implements SessionDriver
{
  var $driver;

  function Session(&$driver)
  {
    $this->driver =& $driver;
  }

  function start()
  {
    session_set_save_handler
    (
       array($this, 'storage_open'),
       array($this, 'storage_close'),
       array($this, 'storage_read'),
       array($this, 'storage_write'),
       array($this, 'storage_destroy'),
       array($this, 'storage_gc')
    );

    session_start();
  }

  function storageOpen()
  {
    $this->driver->storageOpen();
  }

  function storageClose()
  {
    $this->driver->storageClose();
  }

  function storageRead($session_id)
  {
    if($data = $this->driver->storageRead($session_id))
    {
      $this->_includeSessionObjectsClasses($data);
      return $data;
    }
    else
      return false;
  }

  function _includeSessionObjectsClasses($session_data)
  {
    if(preg_match_all('/"*__session_class_path";s:\d+:"([^"]+)"/',
                      $session_data,
                      $matches))
    {
      foreach($matches[1] as $match)
      {
        include_once($match);
      }
    }
  }

  function storageWrite($session_id, $value)
  {
    $this->driver->storageWrite($session_id, $value);
  }

  function storageDestroy($session_id)
  {
    $this->driver->storageDestroy($session_id);
  }

  function storageDestroyUser($user_id)
  {
    $this->driver->storageDestroyUser($user_id);
  }

  function storageGc($max_life_time)
  {
    //???
    if(defined('SESSION_DB_MAX_LIFE_TIME') &&  constant('SESSION_DB_MAX_LIFE_TIME'))
      $max_life_time = constant('SESSION_DB_MAX_LIFE_TIME');

    $this->driver->storageGc($max_life_time);
  }

  function & getReference($name)
  {
    if(!isset($_SESSION[$name]))
      $_SESSION[$name] = '';

    return $_SESSION[$name];
  }

  function get($name, $default_value = null)
  {
    if(!isset($_SESSION[$name]))
      return $default_value;

    return $_SESSION[$name];
  }

  function set($name, $value)
  {
    $_SESSION[$name] = $value;
  }

  function exists($name)
  {
    return isset($_SESSION[$name]);
  }

  function destroy($name)
  {
    if(isset($_SESSION[$name]))
    {
      unset($_SESSION[$name]);
    }
  }
}
?>