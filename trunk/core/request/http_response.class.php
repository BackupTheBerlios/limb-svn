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
	var $headers = array();
		
	function redirect($path)
	{  		
  	$path = str_replace('&amp;', '&', $path);
  	$path = str_replace('//', '/', $path);
  	
  	$this->response_string = "<html><head><meta http-equiv=refresh content='0;url={$path}'></head><body bgcolor=white></body></html>";
	}
	
	function & get_response_string()
	{
	  return $this->response_string;
	}
	
	function is_empty()
	{
	  return $this->response_string == '';
	}
	
	function headers_sent()
	{
	  return sizeof($this->headers) > 0;
	}
	
	function reload()
	{
	  $this->redirect($_SERVER['PHP_SELF']);
	}
		
	function header($header)
	{
	  $this->headers[] = $header;	  
	}
	
	function write($string)
	{
	  $this->response_string = $string;	  
	}
		
	function commit()
	{  	
	  $this->_pre_commit();
  	
  	foreach($this->headers as $header)
  	  $this->_send_header($header);
  	
  	if(!empty($this->response_string))
  	  $this->_send_string($this->response_string);
	  
	  $this->_post_commit();
	}
	
	function _send_header($header)
	{
	  header($header);
	}
	
	function _send_string($string)
	{
	  echo $string;
	}
		
	function _pre_commit()
	{
		while (ob_get_level())
			ob_end_clean();
  
  	ob_start();	
	}
	
	function _post_commit()
	{
    ob_end_flush();
    exit();
	}		
			
} 
?>