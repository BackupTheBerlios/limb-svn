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
require_once(LIMB_DIR . '/core/lib/util/complex_array.class.php');

class uri
{
  var $_protocol = '';

  var $_user = '';

  var $_password = '';

  var $_host = '';

  var $_port = '';

  var $_path = '';

  var $_anchor = '';

  var $_query_items = array();

  var $_path_elements = array();

  function uri($str='')
  {
    if ($str)
      $this->parse($str);
  }

  function get_protocol()
  {
    return $this->_protocol;
  }

  function get_user()
  {
    return $this->_user;
  }

  function get_password()
  {
    return $this->_password;
  }

  function get_host()
  {
    return $this->_host;
  }

  function get_port()
  {
    return $this->_port;
  }

  function get_path()
  {
    return $this->_path;
  }

  function get_anchor()
  {
    return $this->_anchor;
  }

  function parse($str)
  {
    $this->_user        = '';
    $this->_password    = '';
    $this->_host        = '';
    $this->_port        = 80;
    $this->_path        = '';
    $this->_query_items = array();
    $this->_anchor      = '';

    // Only use defaults if not an absolute URL given
    if (!preg_match('/^[a-z0-9]+:\/\//i', $str))
    {
      if (!empty($_SERVER['HTTP_HOST']) && preg_match('/^(.*)(:([0-9]+))?$/U', $_SERVER['HTTP_HOST'], $matches))
      {
        $host = $matches[1];
        if (!empty($matches[3]))
          $port = $matches[3];
        else
          $port = '80';
      }

      $this->_protocol    = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '');
      $this->_user        = '';
      $this->_password        = '';
      $this->_host        = !empty($host) ? $host : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
      $this->_port        = !empty($port) ? $port : (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80);
      $this->_path        = !empty($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '/';
      $this->_query_items = isset($_SERVER['QUERY_STRING']) ? $this->_parse_query_string($_SERVER['QUERY_STRING']) : null;
      $this->_anchor      = '';
    }

    // Parse the url and store the various parts
    if (!empty($str))
    {
      if(!($urlinfo = @parse_url($str)))
        return;

      // Default query_string
      $this->_query_items = array();

      foreach ($urlinfo as $key => $value)
      {
        switch ($key)
        {
          case 'scheme':
            $this->_protocol = $value;
          break;

          case 'user':
            $this->_user = $value;
          break;

          case 'host':
            $this->_host = $value;
          break;

          case 'port':
            $this->_port = $value;
          break;

          case 'pass':
            $this->_password = $value;
          break;

          case 'path':
            if ($value{0} == '/')
              $this->_path = $value;
            else
            {
              $path = dirname($this->_path) == '/' ? '' : dirname($this->_path);//FIXX, not crossplatform?
              $this->_path = sprintf('%s/%s', $path, $value);
            }

            $last_pos = strlen($this->_path)-1;
            if($this->_path{$last_pos} == '/')
              $this->_path = substr($this->_path, 0, $last_pos);								//probably is subject to change

            $this->_path = $this->resolve_path($this->_path);

          break;

          case 'query':
            $this->_query_items = $this->_parse_query_string($value);
          break;

          case 'fragment':
            $this->_anchor = $value;
          break;
        }
      }
    }

    $this->_path_elements = explode('/',$this->_path);
  }

  function count_path()
  {
    return sizeof($this->_path_elements);
  }

  function count_query_items()
  {
    return sizeof($this->_query_items);
  }

  function compare($uri)
  {
    if (
          $this->_protocol != $uri->get_protocol() ||
          $this->_host != $uri->get_host() ||
          $this->_port != $uri->get_port() ||
          $this->_user !== $uri->get_user() ||
          $this->_password !== $uri->get_password()
        )
    return false;

    if(!$this->compare_query($uri))
      return false;

    if($this->compare_path($uri) !== 0)
      return false;

    return true;
  }

  function compare_query($uri)
  {
    if ($this->count_query_items() != $uri->count_query_items())
      return false;

    foreach($this->_query_items as $name => $value)
    {
      if(	(($item = $uri->get_query_item($name)) === false) ||
          $item != $value)
        return false;
    }
    return true;
  }

  function compare_path($uri)
  {
    $count1 = $this->count_path();
    $count2 = $uri->count_path();

    for($i=0; $i < $count1 && $i < $count2; $i++)
    {
      if( $this->get_path_element($i) != $uri->get_path_element($i) )
        return false;
    }

    return ($count1 - $count2);
  }

  function to_string($parts = array('protocol', 'user', 'password', 'host', 'port', 'path', 'query', 'anchor'))
  {
    $string = '';

    if(in_array('protocol', $parts))
      $string .= !empty($this->_protocol) ? $this->_protocol . '://' : '';

    if(in_array('user', $parts))
    {
      $string .=  $this->_user;

      if(in_array('password', $parts))
        $string .= (!empty($this->_password) ? ':' : '') . $this->_password;

      $string .= (!empty($this->_user) ? '@' : '');
    }

    if(in_array('host', $parts))
    {
      $string .= $this->_host;

      if(in_array('port', $parts))
        $string .= (empty($this->_port) || ($this->_port == '80') ? '' : ':' . $this->_port);
    }
    else
      $string = '';

    if(in_array('path', $parts))
      $string .= $this->_path;

    if(in_array('query', $parts))
    {
      $query_string = $this->get_query_string();
      $string .= !empty($query_string) ? '?' . $query_string : '';
    }

    if(in_array('anchor', $parts))
      $string .= !empty($this->_anchor) ? '#' . $this->_anchor : '';

     return $string;
  }

  function get_path_element($level)
  {
    return isset($this->_path_elements[$level]) ? $this->_path_elements[$level] : '';
  }

  function get_path_elements()
  {
    return $this->_path_elements;
  }

  /**
  * Adds a query_string item
  *
  */
  function add_encoded_query_item($name, $value)
  {
    $this->_query_items[$name] = $value;
  }

  function add_query_item($name, $value)
  {
    $this->_query_items[$name] = is_array($value)?
      complex_array :: array_map_recursive('urlencode', $value) :
      urlencode($value);
  }

  function get_query_item($name)
  {
    if (isset($this->_query_items[$name]))
      return $this->_query_items[$name];

    return false;
  }

  function get_query_items()
  {
    return $this->_query_items;
  }

  /**
  * Removes a query_string item
  *
  */
  function remove_query_item($name)
  {
    if (isset($this->_query_items[$name]))
      unset($this->_query_items[$name]);
  }

  /**
  * Sets the query_string to literally what you supply
  */
  function set_query_string($query_string)
  {
    $this->_query_items = $this->_parse_query_string($query_string);
  }

  /**
  * Removes query items
  */
  function remove_query_items()
  {
    $this->_query_items = array();
  }

  /**
  * Returns flat query_string
  *
  */
  function get_query_string()
  {
    $query_string = '';
    $query_items = array();
    $flat_array = array();

    complex_array :: to_flat_array($this->_query_items, $flat_array);

    foreach($flat_array as $key => $value)
    {
      if ($value != '' || is_null($value))
        $query_items[] = $key . '=' . $value;
      else
        $query_items[] = $key;
    }

    if($query_items)
      $query_string = implode('&', $query_items);

    return $query_string;
  }

  /**
  * Parses raw query_string and returns an array of it
  */
  function _parse_query_string($query_string)
  {
    $query_string = rawurldecode($query_string);

    parse_str($query_string, $arr);

    foreach($arr as $key => $item)
    {
      if(!is_array($item))
        $arr[$key] = rawurldecode($item);
    }

    return $arr;
  }

  /**
  * Resolves //, ../ and ./ from a path and returns
  * the result. Eg:
  *
  * /foo/bar/../boo.php    => /foo/boo.php
  * /foo/bar/../../boo.php => /boo.php
  * /foo/bar/.././/boo.php => /foo/boo.php
  *
  */
  function resolve_path($path)
  {
    $path = explode('/', str_replace('//', '/', $path));

    for ($i=0; $i < sizeof($path); $i++)
    {
      if ($path[$i] == '.')
      {
        unset($path[$i]);
        $path = array_values($path);
        $i--;
      }
      elseif ($path[$i] == '..' && ($i > 1 || ($i == 1 && $path[0] != '') ) )
      {
        unset($path[$i]);
        unset($path[$i-1]);
        $path = array_values($path);
        $i -= 2;
      }
      elseif ($path[$i] == '..' && $i == 1 && $path[0] == '')
      {
        unset($path[$i]);
        $path = array_values($path);
        $i--;
      }
      else
        continue;
    }

    return implode('/', $path);
  }

}
?>