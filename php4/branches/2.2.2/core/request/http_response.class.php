<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . 'core/lib/i18n/strings.class.php');

class http_response
{
  var $response_string = '';
  var $response_file_path = '';
  var $headers = array();

  function redirect($path)
  {
    include_once(LIMB_DIR . 'core/template/fileschemes/simpleroot/compiler_support.inc.php');

    $message = strings :: get('redirect_message');//???
    $message = str_replace('%path%', $path, $message);

    if($template = resolve_template_source_file_name('/redirect_template.html'))
    {
      $content = file_get_contents($template);
      $content = str_replace('{$path}', $path, $content);
      $content = str_replace('{$message}', $message, $content);
      $this->response_string =  $content;
    }
    else
      $this->response_string = "<html><head><meta http-equiv=refresh content='0;url={$path}'></head><body bgcolor=white>{$message}</body></html>";
  }

  function reset()
  {
    $this->response_string = '';
    $this->response_file_path = '';
    $this->headers = array();
  }

  function get_status()
  {
    $status = null;
    foreach($this->headers as $header)
    {
      if(preg_match('~^HTTP/1.\d[^\d]+(\d+)[^\d]*~i', $header, $matches))
        $status = (int)$matches[1];
    }

    if($status)
      return $status;
    else
      return 200;
  }

  function get_directive($directive_name)
  {
    $directive = null;
    $regex = '~^' . preg_quote($directive_name). "\s*:(.*)$~i";
    foreach($this->headers as $header)
    {
      if(preg_match($regex, $header, $matches))
        $directive = trim($matches[1]);
    }

    if($directive)
      return $directive;
    else
      return false;
  }

  function get_content_type()
  {
    if($directive = $this->get_directive('content-type'))
      return $directive;
    else
      return 'text/html';
  }

  function & get_response_string()
  {
    return $this->response_string;
  }

  function is_empty()
  {
    $status = $this->get_status();

    return (
      empty($this->response_string) &&
      empty($this->response_file_path) &&
      ($status != 304 && $status != 412));//???
  }

  function headers_sent()
  {
    return sizeof($this->headers) > 0;
  }

  function file_sent()
  {
    return !empty($this->response_file_path);
  }

  function reload()
  {
    $this->redirect($_SERVER['PHP_SELF']);
  }

  function header($header)
  {
    $this->headers[] = $header;
  }

  function readfile($file_path)
  {
    $this->response_file_path = $file_path;
  }

  function write($string)
  {
    $this->response_string .= $string;
  }

  function commit()
  {
    foreach($this->headers as $header)
      $this->_send_header($header);

    if(!empty($this->response_string))
      $this->_send_string($this->response_string);

    if(!empty($this->response_file_path))
      $this->_send_file($this->response_file_path);

    $this->_exit();
  }

  function _send_header($header)
  {
    header($header);
  }

  function _send_string($string)
  {
    echo $string;
  }

  function _send_file($file_path)
  {
    readfile($file_path);
  }

  function _exit()
  {
    exit();
  }
}
?>