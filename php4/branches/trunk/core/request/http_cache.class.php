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
//inspired by http://alexandre.alapetite.net/doc-alex/php-http-304/

define('HTTP_CACHE_TYPE_PRIVATE', 0);
define('HTTP_CACHE_TYPE_PUBLIC', 1);

class http_cache
{
  var $etag;
  var $last_modified_time;
  var $cache_time;
  var $cache_type;

  function http_cache()
  {
    $this->reset();
  }

  function reset()
  {
    $this->last_modified_time = time();
    $this->etag = null;
    $this->cache_time = 0;
    $this->cache_type = HTTP_CACHE_TYPE_PRIVATE;
  }

  function check_and_write(&$response)
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

  function is412()
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

  function is304()
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

  function _write_412_response(&$response)
  {
    $response->header('HTTP/1.1 412 Precondition Failed');
    $response->header('Cache-Control: private, max-age=0, must-revalidate');
    $response->header('Content-Type: text/plain');

    $response->write("HTTP/1.1 Error 412 Precondition Failed: Precondition request failed positive evaluation\n");
  }

  function _write_304_response(&$response)
  {
    $response->header('HTTP/1.0 304 Not Modified');
    $response->header('Etag: ' . $this->get_etag());
    $response->header('Pragma: ');
    $response->header('Cache-Control: ');
    $response->header('Last-Modified: ');
    $response->header('Expires: ');
  }

  function _write_caching_response(&$response)
  {
    $response->header('Cache-Control: ' . $this->_get_cache_control()); //rfc2616-sec14.html#sec14.9
    $response->header('Last-Modified: ' . $this->format_last_modified_time());
    $response->header('Etag: ' . $this->get_etag());
    $response->header('Pragma: ');
    $response->header('Expires: ');
  }

  function _get_cache_control()
  {
    if ($this->cache_time == 0)
      $cache = 'private, must-revalidate, ';
    elseif ($this->cache_type == HTTP_CACHE_TYPE_PRIVATE)
      $cache = 'private, ';
    elseif ($this->cache_type == HTTP_CACHE_TYPE_PUBLIC)
      $cache = 'public, ';
    else
      $cache = '';
    $cache .= 'max-age=' . floor($this->cache_time);
    return $cache;
  }

  function format_last_modified_time()
  {
    return $this->_format_gmt_time($this->last_modified_time);
  }

  function _format_gmt_time($time)
  {
    return gmdate('D, d M Y H:i:s \G\M\T', $time);
  }

  function set_last_modified_time($last_modified_time)
  {
    $this->last_modified_time = $last_modified_time;
  }

  function get_last_modified_time()
  {
    return $this->last_modified_time;
  }

  function set_etag($etag)
  {
    $this->etag = $etag;
  }

  function get_etag()
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

  function _get_script_name()
  {
    if (isset($_SERVER['SCRIPT_FILENAME']))
      return $_SERVER['SCRIPT_FILENAME'];
    elseif (isset($_SERVER['PATH_TRANSLATED']))
      return $_SERVER['PATH_TRANSLATED'];
    else
      return '';
  }

  function set_cache_time($cache_time)
  {
    $this->cache_time = $cache_time;
  }

  function get_cache_time()
  {
    return $this->cache_time;
  }

  function set_cache_type($cache_type)
  {
    $this->cache_type = $cache_type;
  }

  function get_cache_type()
  {
    return $this->cache_type;
  }
}

?>