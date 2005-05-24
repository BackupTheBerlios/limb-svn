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

class HttpResponse// implements Response
{
  var $response_string = '';
  var $response_file_path = '';
  var $headers = array();
  var $is_redirected = false;

  var $redirect_strategy = null;

  function setRedirectStrategy(&$strategy)
  {
    $this->redirect_strategy =& $strategy;
  }

  function redirect($path)
  {
    if ($this->is_redirected)
      return;

    if($this->redirect_strategy === null)
      $strategy =& $this->_getDefaultRedirectStrategy();
    else
      $strategy =& $this->redirect_strategy;

    $strategy->redirect($this, $path);

    $this->is_redirected = true;
  }

  function &_getDefaultRedirectStrategy()
  {
    include_once(dirname(__FILE__) . '/HttpRedirectStrategy.class.php');
    return new HttpRedirectStrategy();
  }

  function reset()
  {
    $this->response_string = '';
    $this->response_file_path = '';
    $this->headers = array();
    $this->is_redirected = false;
  }

  function isRedirected()
  {
    return $this->is_redirected;
  }

  function getStatus()
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

  function getDirective($directive_name)
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

  function getContentType()
  {
    if($directive = $this->getDirective('content-type'))
      return $directive;
    else
      return 'text/html';
  }

  function getResponseString()
  {
    return $this->response_string;
  }

  function isEmpty()
  {
    $status = $this->getStatus();

    return (
      !$this->is_redirected &&
      empty($this->response_string) &&
      empty($this->response_file_path) &&
      ($status != 304 &&  $status != 412));//???
  }

  function headers_sent()
  {
    return sizeof($this->headers) > 0;
  }

  function fileSent()
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
    $this->response_string = $string;
  }

  function append($string)
  {
    $this->response_string .= $string;
  }

  function commit()
  {
    foreach($this->headers as $header)
      $this->_sendHeader($header);

    if(!empty($this->response_string))
      $this->_sendString($this->response_string);

    if(!empty($this->response_file_path))
      $this->_sendFile($this->response_file_path);

    $this->_exit();
  }

  function _sendHeader($header)
  {
    header($header);
  }

  function _sendString($string)
  {
    echo $string;
  }

  function _sendFile($file_path)
  {
    readfile($file_path);
  }

  function _exit()
  {
    exit();
  }
}
?>