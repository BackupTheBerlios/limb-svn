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
require_once(LIMB_DIR . '/core/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/core/lib/util/log.class.php');
require_once(LIMB_DIR . '/core/lib/http/ip.class.php');

class sys
{
  var $line_separator;		// line separator used in files
  var $file_separator;		// directory separator used for files
  var $env_separator;			// list separator used for env variables
  var $request_uri;				// uri which is used for parsing module/view information from, may differ from $_SERVER['REQUEST_URI']
  var $file_system_type;	// type of file_system, is either win32 or unix. This often used to determine os specific paths.
  var $os_type;						// type of file_system, is either win32 or unix. This often used to determine os specific paths.
  var $client_ip;					// type of file_system, is either win32 or unix. This often used to determine os specific paths.
  var $exec_mode = null;	// cli, cgi, module

  /*
   Initializes the object with settings taken from the current script run.
  */
  function sys()
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

    $request_uri = sys::server_variable('REQUEST_URI');

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
          $private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.16\..*/', '/^10..*/', '/^224..*/', '/^240..*/');
          $client_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
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

   /*
   Returns the only legal instance of the sys class.
  */
  function &instance()
  {
    return instantiate_object('sys');
  }

  /*
    return the os type, either "win32", "unix" or "mac"
  */
  function os_type()
  {
    if ( !isset( $this ) || get_class( $this ) != 'sys' )
      $obj =& sys::instance();
    else
      $obj =& $this;

    return $obj->os_type;
  }

  /*
    return the client ip
  */
  function client_ip()
  {
    if ( !isset( $this ) || get_class( $this ) != 'sys' )
      $obj =& sys::instance();
    else
      $obj =& $this;

    return $obj->client_ip;
  }

  /*
   return the file_system type, either "win32" or "unix"
  */
  function file_system_type()
  {
    if ( !isset( $this ) || get_class( $this ) != 'sys' )
      $obj =& sys::instance();
    else
      $obj =& $this;

    return $obj->file_system_type;
  }

  /*
   Returns the string which is used for file separators on the current OS (server).
  */
  function file_separator()
  {
    if ( !isset( $this ) || get_class( $this ) != 'sys' )
      $obj =& sys::instance();
    else
      $obj =& $this;

    return $obj->file_separator;
  }

  /*
   return the backup filename for this platform, returns .bak for win32 and ~ for unix and mac.
  */
  function backup_filename()
  {
    if ( !isset( $this ) || get_class( $this ) != 'sys' )
      $obj =& sys::instance();
    else
      $obj =& $this;

    return $obj->backup_filename;
  }

  /*
   Returns the string which is used for line separators on the current OS (server).
  */
  function line_separator()
  {
    if ( !isset( $this ) || get_class( $this ) != 'sys' )
      $obj =& sys::instance();
    else
      $obj =& $this;

    return $obj->line_separator;
  }

  /*
   Returns the string which is used for enviroment separators on the current OS (server).
  */
  function env_separator()
  {
    if ( !isset( $this ) || get_class( $this ) != 'sys' )
      $obj =& sys::instance();
    else
      $obj =& $this;

    return $obj->env_separator;
  }

  /*
   Returns the current hostname.
  */
  function hostname()
  {
    return sys::server_variable( 'HTTP_HOST' );
  }

  /*
   return the variable named $name in the global $_SERVER variable.
   If the variable is not present an error is shown and null is returned.
  */
  function &server_variable( $name )
  {
    if ( !isset( $_SERVER[$name] ) )
      return null;
    return $_SERVER[$name];
  }

  /*
   Sets the server variable named $name to $value.
   note Variables are only set for the current page view.
  */
  function set_server_variable( $name, $value )
  {
    $_SERVER[$name] = $value;
  }

  /*
   return the path string for the server.
  */
  function &path( $quiet = false )
  {
    return sys::server_variable( 'PATH', $quiet );
  }

  /*
   return the variable named $name in the global $_ENV variable.
  */
  function &environment_variable( $name)
  {
    if ( !isset( $_ENV[$name] ) )
      return null;

    return $_ENV[$name];
  }

  /*
   Sets the environment variable named $name to $value.
   Variables are only set for the current page view.
  */
  function set_environment_variable( $name, $value )
  {
    $_ENV[$name] = $value;
  }

  function exec_mode()
  {
    if ( !isset( $this ) || get_class( $this ) != 'sys' )
      $obj =& sys::instance();
    else
      $obj =& $this;

    return $obj->exec_mode;
  }

  /*
   return the URI used for parsing modules, views and parameters, may differ from $_SERVER['REQUEST_URI'].
  */
  function request_uri()
  {
    if ( !isset( $this ) || get_class( $this ) != 'sys' )
      $obj =& sys::instance();
    else
      $obj =& $this;

    return $obj->request_uri;
  }

}

?>