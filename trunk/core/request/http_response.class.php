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

class http_response
{
	var $response_string = '';
	var $response_file_path = '';
	var $headers = array();
	var $use_client_cache = false;
		
	function redirect($path)
	{  		  	
  	$this->response_string = "
  	<html>
  	<head><meta http-equiv=refresh content='0;url={$path}'></head>
  	<body bgcolor=white>
  	<font color=707070><small>
  	<p>You're being redirected with meta tag...
  	<p>Some browsers may not support this feature, <a href='{$path}'>click here</a> if you're not 
  	redirected in 5 seconds.</small></font>
  	</body>
  	</html>";
	}
	
	function & get_response_string()
	{
	  return $this->response_string;
	}
	
	function is_empty()
	{
	  return (
	    empty($this->response_string) && 
	    empty($this->response_file_path) && 
	    !$this->use_client_cache);
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
	
	function use_client_cache($status = true)
	{
	  $this->use_client_cache = $status;
	  $this->header('HTTP/1.1 304 Not modified');
	}
	
	function write($string)
	{
	  $this->response_string = $string;	  
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