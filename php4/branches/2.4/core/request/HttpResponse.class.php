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

  function redirect($path)
  {
    $this->response_string = "<html><head><meta http-equiv=refresh content='0;url={$path}'></head><body bgcolor=white></body></html>";
  }

  function reset()
  {
    $this->response_string = '';
    $this->response_file_path = '';
    $this->headers = array();
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

define('RELOAD_SELF_URL', '');

function closePopupNoParentReloadResponse()
{
  return "<html><body><script>
          if(window.opener)
          {
              window.opener.focus();
              window.close()
          };
        </script></body></html>";
}

function closePopupResponse(&$request, $parent_reload_url = RELOAD_SELF_URL, $search_for_node = false)
{
  $str = "<html><body><script>
              if(window.opener)
              {";

  if($parent_reload_url != RELOAD_SELF_URL)
    $str .=			"	href = '{$parent_reload_url}';";
  else
    $str .=			"	href = window.opener.location.href;";

  if($search_for_node &&  !$request->hasAttribute('recursive_search_for_node'))
    $str .=			_addJsParamToUrl('href', 'recursive_search_for_node', '1');

  $str .=				_addJsRandomToUrl('href');

  $str .=				"	window.opener.location.href = href;";

  $str .=				" window.opener.focus();
                }
                window.close();
              </script></body></html>";

  return $str;

}

function _addJsRandomToUrl($href)
{
  return _addJsParamToUrl($href, 'rn', 'Math.floor(Math.random()*10000)');
}

function _addJsParamToUrl($href, $param, $value)
{
  return "
    if({$href}.indexOf('?') == -1)
      {$href} = {$href} + '?';

    {$href} = {$href}.replace(/&*rn=[^&]+/g, '');

    items = {$href}.split('#');

    {$href} = items[0] + '&{$param}=' + {$value};

    if(items[1])
      {$href} = {$href} + '#' + items[1];";

}

function redirectPopupResponse($url = '')
{
  if(!$url)
    $url = $_SERVER['PHP_SELF'];

  $str = "<html><body><script>
              if(window.opener)
              {
                href = window.opener.location.href;";

  $str .=				_addJsRandomToUrl('href');

  $str .=				" window.opener.location.href = href;";

  $str .=			"
                window.opener.focus();
              }";

  $str .= "href = '{$url}';";

  $str .= _addJsRandomToUrl('href');

  $str .= "window.location.href = href;";

  $str .= '</script>
          </body>
        </html>';

  return $str;
}
?>