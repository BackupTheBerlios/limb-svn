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
//Inspired by EZpublish(http//ez.no), system class

require_once(LIMB_DIR . '/core/http/Ip.class.php');

class Sys
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
  function Sys()
  {
    $this->_collectSystemParams();
  }

  function & instance()
  {
    if (!isset($GLOBALS['SysGlobalInstance']) || !is_a($GLOBALS['SysGlobalInstance'], 'Sys'))
      $GLOBALS['SysGlobalInstance'] =& new Sys();

    return $GLOBALS['SysGlobalInstance'];
  }

  function _collectSystemParams()
  {
    // Determine OS specific settings
    if (substr( php_uname(), 0, 7) == 'Windows')
    {
      $this->os_type = 'win32';
      $this->file_system_type = 'win32';
      $this->file_separator = '\\';
      $this->line_separator= "\r\n";
      $this->env_separator = ';';
      $this->backup_filename = '.bak';
    }
    elseif (substr( php_uname(), 0, 3 ) == 'Mac')
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

    $request_uri = Sys :: serverVariable('REQUEST_URI');

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

  function osType()
  {
    $inst =& Sys::instance();
    return $inst->os_type;
  }

  function clientIp()
  {
    $inst =& Sys::instance();
    return $inst->client_ip;
  }

  /*
   return the file_system type, either "win32" or "unix"
  */
  function fileSystemType()
  {
    $inst =& Sys::instance();
    return $inst->file_system_type;
  }

  /*
   Returns the string which is used for file separators on the current OS (server).
  */
  function fileSeparator()
  {
    $inst =& Sys::instance();
    return $inst->file_separator;
  }

  /*
   return the backup filename for this platform, returns .bak for win32 and ~ for unix and mac.
  */
  function backupFilename()
  {
    $inst =& Sys::instance();
    return $inst->backup_filename;
  }

  /*
   Returns the string which is used for line separators on the current OS (server).
  */
  function lineSeparator()
  {
    $inst =& Sys::instance();
    return $inst->line_separator;
  }

  /*
   Returns the string which is used for enviroment separators on the current OS (server).
  */
  function envSeparator()
  {
    $inst =& Sys::instance();
    return $inst->env_separator;
  }

  /*
   return the variable named $name in the global $_SERVER variable.
   If the variable is not present an error is shown and null is returned.
  */
  function serverVariable($name)
  {
    if (isset($_SERVER[$name]))
      return $_SERVER[$name];
  }

  /*
   Sets the server variable named $name to $value.
   note Variables are only set for the current page view.
  */
  function setServerVariable($name, $value)
  {
    $_SERVER[$name] = $value;
  }

  /*
   return the variable named $name in the global $_ENV variable.
  */
  function environmentVariable($name)
  {
    if (isset($_ENV[$name]))
      return $_ENV[$name];
  }

  /*
   Sets the environment variable named $name to $value.
   Variables are only set for the current page view.
  */
  function setEnvironmentVariable($name, $value)
  {
    $_ENV[$name] = $value;
  }

  function execMode()
  {
    $inst =& Sys::instance();
    return $inst->exec_mode;
  }

  function requestUri()
  {
    $inst =& Sys::instance();
    return $inst->request_uri;
  }
}

?>