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
  	$message = strings :: get('redirect_message');//???
  	$message = str_replace('%path%', $path, $message);
  	$this->response_string = "<html><head><meta http-equiv=refresh content='0;url={$path}'></head><body bgcolor=white><font color=707070><small>{$message}</small></font></body></html>";
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

define('RELOAD_SELF_URL', '');

function close_popup_no_parent_reload_response()
{	
	return "<html><body><script>
					if(window.opener)
					{													
				 			window.opener.focus();
				 			window.close()
				 	};
				</script></body></html>"; 
}

function close_popup_response(&$request, $parent_reload_url = RELOAD_SELF_URL, $search_for_node = false)
{	
	$str = "<html><body><script>
							if(window.opener)
							{";
														
	if($parent_reload_url != RELOAD_SELF_URL)
		$str .=			"	href = '{$parent_reload_url}';";
	else	
		$str .=			"	href = window.opener.location.href;"; 

	if($search_for_node && !$request->has_attribute('recursive_search_for_node'))
		$str .=			_add_js_param_to_url('href', 'recursive_search_for_node', '1');
		
	$str .=				_add_js_random_to_url('href');
	
	$str .=				"	window.opener.location.href = href;";			
		
	$str .=				" window.opener.focus();
								}
								window.close();
							</script></body></html>"; 
							
	return $str;
	
}

function _add_js_random_to_url($href)
{
	return _add_js_param_to_url($href, 'rn', 'Math.floor(Math.random()*10000)');
}

function _add_js_param_to_url($href, $param, $value)
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

function redirect_popup_response($url = '')
{
	if(!$url)
		$url = $_SERVER['PHP_SELF'];
	
	$str = "<html><body><script>
							if(window.opener)
							{
								href = window.opener.location.href;";
								
	$str .=				_add_js_random_to_url('href');
	
	$str .=				" window.opener.location.href = href;";
								
	$str .=			"	
								window.opener.focus();
							}"; 

	$str .= "href = '{$url}';"; 
		
	$str .= _add_js_random_to_url('href');
							
	$str .= "window.location.href = href;";
							
	$str .= '</script>
					</body>
				</html>';
						
	return $str;
}
?>