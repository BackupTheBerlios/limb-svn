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
require_once(LIMB_DIR . '/class/core/session_driver.interface.php');

class session implements session_driver
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
  
  public function storage_open()
  {
    $this->driver->storage_open();  
  }
  
  public function storage_close()
  {
    $this->driver->storage_close();
  }
  
  public function storage_read($session_id)
  {
    if($data = $this->driver->storage_read($session_id))
    { 
      $this->_include_session_objects_classes($data);
      return $data;
    }
    else
      return false;
  }
  
  protected function _include_session_objects_classes($session_data)
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
  
  public function storage_write($session_id, $value)
  {
    $this->driver->storage_write($session_id, $value);
  }
  
  public function storage_destroy($session_id)
  {
    $this->driver->storage_destroy($session_id);
  }

  public function storage_destroy_user($user_id)
  {
    $this->driver->storage_destroy_user($user_id);
  }
  
  public function storage_gc($max_life_time)
  {
    //???
    if(defined('SESSION_DB_MAX_LIFE_TIME') && constant('SESSION_DB_MAX_LIFE_TIME'))
      $max_life_time = constant('SESSION_DB_MAX_LIFE_TIME');
    
    $this->driver->storage_gc($max_life_time);
  }
  
  public function & get_reference($name)
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