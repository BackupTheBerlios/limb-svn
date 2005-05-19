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

class sys
{
  var $line_separator;
  var $file_separator;
  var $env_separator;
  var $file_system_type;
  var $os_type;
  var $client_ip;
  var $exec_mode = null;

  function sys()
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

  function &instance()
  {
    return instantiate_object('sys');
  }

  function os_type()
  {
    $obj =& sys::instance();
    return $obj->os_type;
  }

  function is_win32()
  {
    return sys :: os_type() == 'win32';
  }

  function is_unix()
  {
    return sys :: os_type() == 'unix';
  }

  function is_mac()
  {
    return sys :: os_type() == 'mac';
  }

  function is_module()
  {
    return sys :: exec_mode() == 'module';
  }

  function is_cgi()
  {
    return sys :: exec_mode() == 'cgi';
  }

  function client_ip()
  {
    $obj =& sys::instance();
    return $obj->client_ip;
  }

  function file_system_type()
  {
    $obj =& sys::instance();
    return $obj->file_system_type;
  }

  function file_separator()
  {
    $obj =& sys::instance();
    return $obj->file_separator;
  }

  function backup_filename()
  {
    $obj =& sys::instance();
    return $obj->backup_filename;
  }

  function line_separator()
  {
    $obj =& sys::instance();
    return $obj->line_separator;
  }

  function env_separator()
  {
    $obj =& sys::instance();
    return $obj->env_separator;
  }

  function exec_mode()
  {
    $obj =& sys::instance();
    return $obj->exec_mode;
  }
}

?>