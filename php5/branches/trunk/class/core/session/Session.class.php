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
require_once(LIMB_DIR . '/class/core/session/SessionDriver.interface.php');

class Session implements SessionDriver
{
  protected $driver;

  function __construct($driver)
  {
    $this->driver = $driver;
  }

  public function start()
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

  public function storageOpen()
  {
    $this->driver->storageOpen();
  }

  public function storageClose()
  {
    $this->driver->storageClose();
  }

  public function storageRead($session_id)
  {
    if($data = $this->driver->storageRead($session_id))
    {
      $this->_includeSessionObjectsClasses($data);
      return $data;
    }
    else
      return false;
  }

  protected function _includeSessionObjectsClasses($session_data)
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

  public function storageWrite($session_id, $value)
  {
    $this->driver->storageWrite($session_id, $value);
  }

  public function storageDestroy($session_id)
  {
    $this->driver->storageDestroy($session_id);
  }

  public function storageDestroyUser($user_id)
  {
    $this->driver->storageDestroyUser($user_id);
  }

  public function storageGc($max_life_time)
  {
    //???
    if(defined('SESSION_DB_MAX_LIFE_TIME') &&  constant('SESSION_DB_MAX_LIFE_TIME'))
      $max_life_time = constant('SESSION_DB_MAX_LIFE_TIME');

    $this->driver->storageGc($max_life_time);
  }

  public function & getReference($name)
  {
    if(!isset($_SESSION[$name]))
      $_SESSION[$name] = '';

    return $_SESSION[$name];
  }

  public function get($name, $default_value = null)
  {
    if(!isset($_SESSION[$name]))
      return $default_value;

    return $_SESSION[$name];
  }

  public function set($name, $value)
  {
    $_SESSION[$name] = $value;
  }

  public function exists($name)
  {
    return isset($_SESSION[$name]);
  }

  public function destroy($name)
  {
    if(isset($_SESSION[$name]))
    {
      unset($_SESSION[$name]);
    }
  }
}
?>