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
  var $line_separator;
  var $file_separator;
  var $env_separator;
  var $file_system_type;
  var $os_type;
  var $client_ip;
  var $exec_mode = null;

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
    if (substr(php_uname(), 0, 7) == 'Windows')
    {
      $this->os_type = 'win32';
      $this->file_system_type = 'win32';
      $this->file_separator = '\\';
      $this->line_separator= "\r\n";
      $this->env_separator = ';';
      $this->backup_filename = '.bak';
    }
    elseif (substr(php_uname(), 0, 3) == 'Mac')
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

    if(isset($_SERVER['REMOTE_ADDR']))
    {
      $client_ip = $_SERVER['REMOTE_ADDR'];

      if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      {
        if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $_SERVER['HTTP_X_FORWARDED_FOR'], $ip_list))
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

  function osType()
  {
    $inst =& Sys::instance();
    return $inst->os_type;
  }

  function isWin32()
  {
    return Sys :: osType() == 'win32';
  }

  function isUnix()
  {
    return Sys :: osType() == 'unix';
  }

  function isMac()
  {
    return Sys :: osType() == 'mac';
  }

  function isModule()
  {
    return Sys :: execMode() == 'module';
  }

  function isCgi()
  {
    return Sys :: execMode() == 'cgi';
  }

  function clientIp()
  {
    $inst =& Sys::instance();
    return $inst->client_ip;
  }

  function fileSystemType()
  {
    $inst =& Sys::instance();
    return $inst->file_system_type;
  }

  function fileSeparator()
  {
    $inst =& Sys::instance();
    return $inst->file_separator;
  }

  function backupFilename()
  {
    $inst =& Sys::instance();
    return $inst->backup_filename;
  }

  function lineSeparator()
  {
    $inst =& Sys::instance();
    return $inst->line_separator;
  }

  function envSeparator()
  {
    $inst =& Sys::instance();
    return $inst->env_separator;
  }

  function execMode()
  {
    $inst =& Sys::instance();
    return $inst->exec_mode;
  }
}

?>