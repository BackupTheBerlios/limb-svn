<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
//inspired by http://alexandre.alapetite.net/doc-alex/php-http-304/

class http_cache
{
  const TYPE_PRIVATE = 0;
  const TYPE_PUBLIC = 1;
  
  protected $etag;
  protected $last_modified_time;
  protected $cache_time;
  protected $cache_type;
  
  function __construct()
  {
    $this->reset();
  }
  
  public function reset()
  {
    $this->last_modified_time = time();
    $this->etag = null;
    $this->cache_time = 0;
    $this->cache_type = http_cache :: TYPE_PRIVATE;
  }
  
  public function check_and_write($response)
  {
    if($this->is412())
    {
      $this->_write_412_response($response);
      return true;
    }
    elseif($this->is304())
    {
      $this->_write_304_response($response);
      return true;
    }
    else
    { 
      $this->_write_caching_response($response);
      return $_SERVER['REQUEST_METHOD'] == 'HEAD'; //rfc2616-sec9.html#sec9.4    
    }
  }
  
  public function is412()
  {
    if (isset($_SERVER['HTTP_IF_MATCH'])) //rfc2616-sec14.html#sec14.24
    {
      $etag_client = stripslashes($_SERVER['HTTP_IF_MATCH']);
      return (($etag_client != '*') && (strpos($etag_client, $this->get_etag()) === false));
    }
    
    if (isset($_SERVER['HTTP_IF_UNMODIFIED_SINCE'])) //http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.28
    {
      return (strcasecmp($_SERVER['HTTP_IF_UNMODIFIED_SINCE'], $this->format_last_modified_time()) != 0);
    }
      
    return false;
  }
  
  public function is304()
  {
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) //rfc2616-sec14.html#sec14.25 //rfc1945.txt
    {
      return ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $this->format_last_modified_time());
    }
    
    if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) //rfc2616-sec14.html#sec14.26
    {
      $etag_client = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);
      return (($etag_client == $this->get_etag()) || ($etag_client == '*'));
    }
    
    return false;  
  }

  protected function _write_412_response($response)
  {
    $response->header('HTTP/1.1 412 Precondition Failed');
    $response->header('Cache-Control: protected, max-age=0, must-revalidate');
    $response->header('Content-Type: text/plain');
    
    $response->write("HTTP/1.1 Error 412 Precondition Failed: Precondition request failed positive evaluation\n");
  }
  
  protected function _write_304_response($response)
  {
    $response->header('HTTP/1.0 304 Not Modified');
    $response->header('Etag: ' . $this->get_etag()); 
    $response->header('Pragma: ');
    $response->header('Cache-Control: ');
    $response->header('Last-Modified: ');
    $response->header('Expires: ');
  }
  
  protected function _write_caching_response($response)
  { 
    $response->header('Cache-Control: ' . $this->_get_cache_control()); //rfc2616-sec14.html#sec14.9
    $response->header('Last-Modified: ' . $this->format_last_modified_time());
    $response->header('Etag: ' . $this->get_etag()); 
    $response->header('Pragma: ');
    $response->header('Expires: ');
  }
  
  protected function _get_cache_control()
  {
    if ($this->cache_time == 0)
      $cache = 'protected, must-revalidate, ';
    elseif ($this->cache_type == http_cache :: TYPE_PRIVATE) 
      $cache = 'protected, ';
    elseif ($this->cache_type == http_cache :: TYPE_PUBLIC) 
      $cache = 'public, ';
    else 
      $cache = '';
    $cache .= 'max-age=' . floor($this->cache_time);      
    return $cache;  
  }
  
  public function format_last_modified_time()
  {
    return $this->_format_gmt_time($this->last_modified_time);
  }
  
  protected function _format_gmt_time($time)
  {
    return gmdate('D, d M Y H:i:s \G\M\T', $time);
  }

  public function set_last_modified_time($last_modified_time)
  {
    $this->last_modified_time = $last_modified_time;
  }
  
  public function get_last_modified_time()
  {
    return $this->last_modified_time;
  } 

  public function set_etag($etag)
  {
    $this->etag = $etag;
  }
  
  public function get_etag()
  {
    if($this->etag)
      return $this->etag;
    
    //rfc2616-sec14.html#sec14.19 //='"0123456789abcdef0123456789abcdef"'
    if (isset($_SERVER['QUERY_STRING'])) 
      $query = '?' . $_SERVER['QUERY_STRING'];
    else 
      $query = '';
    
    $this->etag = '"' . md5($this->_get_script_name() . $query . '#' . $this->last_modified_time ) . '"';
    
    return $this->etag;
  } 
  
  protected function _get_script_name()
  {
    if (isset($_SERVER['SCRIPT_FILENAME']))
      return $_SERVER['SCRIPT_FILENAME'];
    elseif (isset($_SERVER['PATH_TRANSLATED'])) 
      return $_SERVER['PATH_TRANSLATED'];
    else 
      return '';  
  }
    
  public function set_cache_time($cache_time)
  {
    $this->cache_time = $cache_time;
  }
  
  public function get_cache_time()
  {
    return $this->cache_time;
  }  
  
  public function set_cache_type($cache_type)
  {
    $this->cache_type = $cache_type;
  }
  
  public function get_cache_type()
  {
    return $this->cache_type;
  }   
}

?>