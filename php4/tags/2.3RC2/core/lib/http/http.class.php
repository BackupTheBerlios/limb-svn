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

class http
{
  var $_method = 'POST';
  var $_host = '';
  var $_path = '\\';
  var $_port = '80';
  var $_referer = '';
  var $_user_agent = '';
  var $_enctype = 'application/x-www-form-urlencoded';
  var $_element = array();
  var $_timeout = 20;

  function http()
  {
    $this->_host = $_SERVER['HTTP_HOST'];
    $this->_referer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : '';
    $this->_user_agent = $_SERVER['HTTP_USER_AGENT'];
  }

  function set_method($method = 'POST')
  {
    $this->_method = strtoupper($method);
  }

  function set_host($host = '')
  {
    if(empty($host))
      $host = $HTTP_HOST;
    $this->_host = $host;
  }

  function set_port($port = '80')
  {
    $port = intval($port);
    if($port	< 0	|| $port	> 65535)
      $port = 80;
    $this->_port = $port;
  }

  function set_referer($referer = '')
  {
    $this->_referer = $referer;
  }

  function set_user_agent($user_agent = '')
  {
    $this->_user_agent = $user_agent;
  }

  function set_path($path = '\\')
  {
    $this->_path = $path;
  }

  function set_action($action = "")
  {
    $url = parse_url($action);

    if(!empty($url['host']))
      $this->_host = $url['host'];

    if(!empty($url['path']))
      $this->_path = $url['path'];

    if(!empty($url['port']))
      $this->_port = $url['port'];
  }

  function set_enctype($enctype = 'application/x-www-form-urlencoded')
  {
    if($enctype != 'application/x-www-form-urlencoded' &&	$enctype != 'multipart/form-data')
      $enctype = 'application/x-www-form-urlencoded';
    $this->_enctype = $enctype;
  }

  function set_elements($elements)
  {
    foreach($elements as $key => $value)
      $this->_element[$key] = $value;
  }

  function set_element($key = '',$val = '')
  {
    $this->_element[$key] = $val;
  }

  function set_timeout($timeout = 20)
  {
    $timeout = intval($timeout);
    if($timeout<1)
      $timeout = 1;
    $this->_timeout = $timeout;
  }

  function send_request()
  {
    $errno = $errstr = $retstr = '';
    $sk	= fsockopen($this->_host, $this->_port,	&$errno, &$errstr, $this->_timeout);
    if(!$sk)
    {
      $this->_errno = $errno;
      $this->_errstr = $errstr;
      return false;
    }
    else
    {
      $request  = "{$this->_method} {$this->_path} HTTP/1.0 \r\n";
      if (!empty($this->_referer))
        $request .= "Referer: {$this->_referer}\r\n";
      if (!empty($this->_user_agent))
        $request .= "User-Agent: {$this->_user_agent} \r\n";
      $request .= "Host: {$this->_host}\r\n";
      $request .= "Content-type: {$this->_enctype}";

      $boundary = '----' . md5(uniqid(rand())) . '----';
      $message = $this->_get_message($boundary);

      if($this->_enctype == "multipart/form-data")
        $request .= "; boundary={$boundary}\r\n";

      if (!empty($message))
      {
        $request .= "Content-length: {$message}\r\n\r\n";
        $request .= $message;
      }
      else
        $request .= "\r\n\r\n";

      fputs($sk, $request);

      $data = '';
      while(!feof($sk))
      {
        $data .= fread($sk, 128);
      }

      $cpos = strpos($data, "\r\n\r\n");

      $this->_headers = substr($data, 0, $cpos);
      $this->_contents = substr($data, $cpos + 4);

      fclose($sk);
      return true;
    }
    return false;
  }

  function get_response()
  {
    return $this->_headers . "\r\n\r\n" . $this->_contents;
  }

  function get_headers()
  {
    return $this->_headers;
  }

  function get_contents()
  {
    return $this->_contents;
  }

  function get_errno()
  {
    return $this->_errno;
  }

  function get_errstr()
  {
    return $this->_errstr;
  }

  function _get_message($boundary = "")
  {
    $message = '';

    $switch = ($this->_enctype == 'multipart/form-data') ? 0 : 1;

    foreach($this->_element as $key => $value)
    {
      if($switch)
      {
        if(!empty($message))
          $message .= "&";
        $message .= rawurlencode($key) . '=' . rawurlencode($value);
      }
      else
      {
        $message .= $boundary."\r\n";
        $message .= "Content-Disposition: form-data; ";
        $message .= "name=\"{$key}\"\r\n\r\n{$value}\r\n\r\n";
      }
    }
    if(!$switch)
      $message .= $boundary."\r\n";
    return $message;
  }
}

?>