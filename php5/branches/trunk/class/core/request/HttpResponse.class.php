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
require_once(LIMB_DIR . '/class/core/request/Response.interface.php');

class HttpResponse implements Response
{
  protected $response_string = '';
  protected $response_file_path = '';
  protected $headers = array();

  public function redirect($path)
  {
    include_once(LIMB_DIR . '/class/i18n/Strings.class.php');

    $message = Strings :: get('redirect_message');//???
    $message = str_replace('%path%', $path, $message);
    $this->response_string = "<html><head><meta http-equiv=refresh content='0;url={$path}'></head><body bgcolor=white><font color=707070><small>{$message}</small></font></body></html>";
  }

  public function reset()
  {
    $this->response_string = '';
    $this->response_file_path = '';
    $this->headers = array();
  }

  public function getStatus()
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

  public function getDirective($directive_name)
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

  public function getContentType()
  {
    if($directive = $this->getDirective('content-type'))
      return $directive;
    else
      return 'text/html';
  }

  public function getResponseString()
  {
    return $this->response_string;
  }

  public function isEmpty()
  {
    $status = $this->getStatus();

    return (
      empty($this->response_string) && 
      empty($this->response_file_path) && 
      ($status != 304 &&  $status != 412));//???
  }

  public function headers_sent()
  {
    return sizeof($this->headers) > 0;
  }

  public function fileSent()
  {
    return !empty($this->response_file_path);
  }

  public function reload()
  {
    $this->redirect($_SERVER['PHP_SELF']);
  }

  public function header($header)
  {
    $this->headers[] = $header;
  }

  public function readfile($file_path)
  {
    $this->response_file_path = $file_path;
  }

  public function write($string)
  {
    $this->response_string = $string;
  }

  public function commit()
  {
    foreach($this->headers as $header)
      $this->_sendHeader($header);

    if(!empty($this->response_string))
      $this->_sendString($this->response_string);

    if(!empty($this->response_file_path))
      $this->_sendFile($this->response_file_path);

    $this->_exit();
  }

  protected function _sendHeader($header)
  {
    header($header);
  }

  protected function _sendString($string)
  {
    echo $string;
  }

  protected function _sendFile($file_path)
  {
    readfile($file_path);
  }

  protected function _exit()
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

function closePopupResponse($request, $parent_reload_url = RELOAD_SELF_URL, $search_for_node = false)
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