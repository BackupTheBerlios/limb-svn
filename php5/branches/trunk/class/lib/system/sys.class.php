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
//Inspired by EZpublish(http//ez.no), system class

require_once(LIMB_DIR . 'class/lib/http/ip.class.php');

class sys
{
  protected static $instance = null;
   
  protected $line_separator;		// line separator used in files
  protected $file_separator;		// directory separator used for files
  protected $env_separator;			// list separator used for env variables
  protected $request_uri;				// uri which is used for parsing module/view information from, may differ from $_SERVER['REQUEST_URI']
  protected $file_system_type;	// type of file_system, is either win32 or unix. This often used to determine os specific paths.
  protected $os_type;						// type of file_system, is either win32 or unix. This often used to determine os specific paths.
  protected $client_ip;					// type of file_system, is either win32 or unix. This often used to determine os specific paths.
  protected $exec_mode = null;	// cli, cgi, module

  /*
   Initializes the object with settings taken from the current script run.
  */
  function __construct()
  {
    $this->_collect_system_params();
  }
  
	static public function instance()
	{
    if (!self :: $instance)
      self :: $instance = new sys();

    return self :: $instance;	
	}  
	
	protected function _collect_system_params()
	{
    // Determine OS specific settings
    if ( substr( php_uname(), 0, 7 ) == 'Windows' )
    {
      $this->os_type = 'win32';
      $this->file_system_type = 'win32';
      $this->file_separator = '\\';
      $this->line_separator= "\r\n";
      $this->env_separator = ';';
      $this->backup_filename = '.bak';
    }
    elseif ( substr( php_uname(), 0, 3 ) == 'Mac' )
    {
      $this->os_type = 'mac';
      $this->file_system_type = 'unix';
      $this->file_separator = '/';
      $this->line_separator= "\r";
      $this->env_separator = ':';
      $this->backup_filename = '~';
    }
    else
    {
      $this->os_type = 'unix';
      $this->file_system_type = 'unix';
      $this->file_separator = '/';
      $this->line_separator= "\n";
      $this->env_separator = ':';
      $this->backup_filename = '~';
    }
    
    $request_uri = self :: server_variable('REQUEST_URI');

    // Remove url parameters
    if ( ereg( "([^?]+)", $request_uri, $regs ) )
    	$request_uri = $regs[1];

    // Remove internal links
    if ( ereg( "([^#]+)", $request_uri, $regs ) )
    	$request_uri = $regs[1];

    $this->request_uri = $request_uri;
    
    if(isset($_SERVER['REMOTE_ADDR']))
    {
    	$client_ip = $_SERVER['REMOTE_ADDR'];
    
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $_SERVER['HTTP_X_FORWARDED_FOR'], $ip_list))
				{
					$protected_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.16\..*/', '/^10..*/', '/^224..*/', '/^240..*/');
					$client_ip = preg_replace($protected_ip, $client_ip, $ip_list[1]);
				}
			}

			$this->client_ip = $client_ip;
		}
		
		if(php_sapi_name() == 'cli')
			$this->exec_mode = 'cli';
		elseif(substr(php_sapi_name(),0,3) == 'cgi')
			$this->exec_mode = 'cgi';
		elseif($_SERVER['GATEWAY_INTERFACE'])
			$this->exec_mode = 'module';	
	}
  
  public function os_type()
  {    	
    return sys::instance()->os_type;
  }

  public function client_ip()
  {
    return sys::instance()->client_ip;
	}
			
  /*
   return the file_system type, either "win32" or "unix"
  */
  public function file_system_type()
  {    	
    return sys::instance()->file_system_type;
  }

  /*
   Returns the string which is used for file separators on the current OS (server).
  */
  public function file_separator()
  {
    return sys::instance()->file_separator;
  }

  /*
   return the backup filename for this platform, returns .bak for win32 and ~ for unix and mac.
  */
  public function backup_filename()
  {
    return sys::instance()->backup_filename;
  }

  /*
   Returns the string which is used for line separators on the current OS (server).
  */
  public function line_separator()
  {   	
    return sys::instance()->line_separator;
  }

  /*
   Returns the string which is used for enviroment separators on the current OS (server).
  */
  public function env_separator()
  {
    return sys::instance()->env_separator;
  }

  /*
   return the variable named $name in the global $_SERVER variable.
   If the variable is not present an error is shown and null is returned.
  */
  public function server_variable($name)
  {
    if (isset($_SERVER[$name]))
      return $_SERVER[$name];
  }

  /*
   Sets the server variable named $name to $value.
   note Variables are only set for the current page view.
  */
  public function set_server_variable($name, $value)
  {
    $_SERVER[$name] = $value;
  }

  /*
   return the variable named $name in the global $_ENV variable.
  */
  public function environment_variable($name)
  {
    if (isset($_ENV[$name]))
      return $_ENV[$name];
  }

  /*
   Sets the environment variable named $name to $value.
   Variables are only set for the current page view.
  */
  public function set_environment_variable($name, $value)
  {
    $_ENV[$name] = $value;
  }
  
  public function exec_mode()
  {
  	return sys::instance()->exec_mode;
  }

  public function request_uri()
  {
    return sys::instance()->request_uri;
  }
}

?>