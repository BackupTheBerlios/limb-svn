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
require_once(LIMB_DIR . '/core/lib/i18n/strings.class.php');

class http_response
{
  var $response_string = '';
  var $response_file_path = '';
  var $headers = array();
  var $is_redirected = false;

  var $redirect_strategy = null;

  function set_redirect_strategy(&$strategy)
  {
    $this->redirect_strategy =& $strategy;
  }

  function redirect($path)
  {
    if ($this->is_redirected)
      return;

    if($this->redirect_strategy === null)
      $strategy =& $this->_get_default_redirect_strategy();
    else
      $strategy =& $this->redirect_strategy;

    $strategy->redirect($this, $path);

    $this->is_redirected = true;
  }

  function &_get_default_redirect_strategy()
  {
    include_once(dirname(__FILE__) . '/http_redirect_strategy.class.php');
    return new http_redirect_strategy();
  }

  function reset()
  {
    $this->response_string = '';
    $this->response_file_path = '';
    $this->headers = array();
    $this->is_redirected = false;
  }

  function is_redirected()
  {
    return $this->is_redirected;
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

    return (!$this->is_redirected &&
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