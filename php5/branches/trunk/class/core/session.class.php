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

class session
{
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
	
	public function session_exists($name)
	{
		return isset($_SESSION[$name]);
	}

	public function destroy($name)
	{
		if(isset($_SESSION[$name]))
		{
			session_unregister($name);
		  unset($_SESSION[$name]);
		}
	}	
}
?>