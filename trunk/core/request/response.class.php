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
define('REQUEST_IMMEDIATE_MODE', 1);
define('REQUEST_DEFERRED_MODE', 2);

class response
{
	var $response_string = '';
	var $headers = array();
	
	function response()
	{
	}
	
	function & instance()
	{
		$obj =& instantiate_object('response');
		return $obj; 	
	}
	
	function redirect($path)
	{  		
  	$path = str_replace('&amp;', '&', $path);
  	$path = str_replace('//', '/', $path);
  	
  	$this->response_string = "<html><head><meta http-equiv=refresh content='0;url={$path}'></head><body bgcolor=white></body></html>";
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
	
	function write_response_string($string)
	{
	  $this->response_string = $string;	  
	}
		
	function commit()
	{  	
	  $this->_pre_commit();
  	
  	foreach($this->headers as $header)
  	  $this->_write_header($header);
  	
  	if(!empty($this->response_string))
  	  $this->_write_string($this->response_string);
	  
	  $this->_post_commit();
	}
	
	function _write_header($header)
	{
	  header($header);
	}
	
	function _write_string($string)
	{
	  echo $string;
	}
		
	function _pre_commit()
	{
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