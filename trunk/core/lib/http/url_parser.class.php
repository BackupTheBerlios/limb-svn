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

class url_parser
{
  var $url = '';
  
  var $protocol = '';

  var $username = '';

  var $password = '';

  var $host = '';
  
  var $port = '';
  
  var $path = '';
  
  var $anchor = '';
  
  var $_query_items = array();

  var $_use_brackets = true;
	
  var $_path_elements = array();

  function url_parser($url='', $use_brackets=true)
  {
    if ($url)
      $this->parse($url, $use_brackets);
  }
  
  function parse($url, $use_brackets=true)
  {
		$this->_use_brackets = $use_brackets;
		$this->url         = $url;
		$this->user        = '';
		$this->pass        = '';
		$this->host        = '';
		$this->port        = 80;
		$this->path        = '';
		$this->_query_items = array();
		$this->anchor      = '';

		// Only use defaults if not an absolute URL given
		if (!preg_match('/^[a-z0-9]+:\/\//i', $url)) 
		{
      if (!empty($_SERVER['HTTP_HOST']) && preg_match('/^(.*)(:([0-9]+))?$/U', $_SERVER['HTTP_HOST'], $matches))
      {
      	$host = $matches[1];
        if (!empty($matches[3]))
        	$port = $matches[3];
        else
         	$port = '80';			          
      }

      $this->protocol    = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '');
      $this->user        = '';
      $this->pass        = '';
      $this->host        = !empty($host) ? $host : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
      $this->port        = !empty($port) ? $port : (isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80);
      $this->path        = !empty($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '/';
      $this->_query_items = isset($_SERVER['QUERY_STRING']) ? $this->_parse_query_string($_SERVER['QUERY_STRING']) : null;
      $this->anchor      = '';
		}

    // Parse the url and store the various parts
    if (!empty($url))		
		{
      if(!($urlinfo = @parse_url($url)))
      	return;

      // Default query_string
      $this->_query_items = array();

      foreach ($urlinfo as $key => $value) 
      {
        switch ($key) 
        {
          case 'scheme':
          	$this->protocol = $value;
          break;
          
          case 'user':
          case 'pass':
          case 'host':
          case 'port':
          	$this->$key = $value;
          break;

          case 'path':
            if ($value{0} == '/')
            	$this->path = $value;
            else 
            {
            	$path = dirname($this->path) == '/' ? '' : dirname($this->path);//FIXX, not crossplatform?
            	$this->path = sprintf('%s/%s', $path, $value);
            }
            
    				$last_pos = strlen($this->path)-1;
  					if($this->path{$last_pos} == '/')
  						$this->path = substr($this->path, 0, $last_pos);								//probably is subject to change
        
          break;
          
          case 'query':
          	$this->_query_items = $this->_parse_query_string($value);
          break;

          case 'fragment':
          	$this->anchor = $value;
          break;
        }
      }
    }
  	  		
    $this->_path_elements = explode('/',$this->path);
  }

  function count_path()
  {
    return sizeof($this->_path_elements);
  }
  
  function compare($url, &$rest, &$query_match)
  { 
  	$rest = null;
  		
  	$url_parser = new url_parser($url);
  	
  	$count1 = $this->count_path();
  	$count2 = $url_parser->count_path();
  	
    if (  !$count1 ||
    			!$count2 ||
    			$this->protocol !== $url_parser->protocol ||
    			$this->host !== $url_parser->host
    		)
    return false;
        
    $query_match = false;
    
    if(sizeof($this->_query_items) == sizeof($url_parser->_query_items))
    {
    	$query_match = true;
    	
	    foreach($this->_query_items as $name => $value)
	    {
	    	if(	!isset($url_parser->query_items[$name]) ||
	    			$url_parser->query_items[$name] != $this->_query_items[$name])
	    	{
	    		$query_match = false;
	    		break;
	    	}
	  	}
	  }
    
    for($i=0; $i < $count1 && $i < $count2; $i++)
    {
      if( $this->_path_elements[$i] != $url_parser->_path_elements[$i] )
      	return false;
    }
    $rest = ($count1 - $count2);
    return true;
  }

  function get_url()
  {
    $query_string = $this->get_query_string();

    $this->url = $this->protocol . '://'
               . $this->user . (!empty($this->pass) ? ':' : '')
               . $this->pass . (!empty($this->user) ? '@' : '')
               . $this->host . ($this->port == '80' ? '' : ':' . $this->port)
               . $this->path
               . (!empty($query_string) ? '?' . $query_string : '')
               . (!empty($this->anchor) ? '#' . $this->anchor : '');

    return $this->url;
  }
  
  function get_inner_url()
  {
    $query_string = $this->get_query_string();

    $url = $this->path
           . (!empty($query_string) ? '?' . $query_string : '')
           . (!empty($this->anchor) ? '#' . $this->anchor : '');
           
     
		return $url;
  }
  
  function is_inner()
  {
  	return ($this->host == $_SERVER['HTTP_HOST']);
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
  function add_query_item($name, $value, $preencoded = false)
  {
    $this->_query_items[$name] = $preencoded ? $value : urlencode($value);
    
    if ($preencoded)
    	$this->_query_items[$name] = $value;
    else
    	$this->_query_items[$name] = is_array($value)? array_map('urlencode', $value): urlencode($value);
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
    if (!empty($this->_query_items)) 
    {
      foreach ($this->_query_items as $name => $value) 
      {
        if (is_array($value)) 
        {
          foreach ($value as $k => $v)
          	$query_string[] = $this->_use_brackets ? sprintf('%s[%s]=%s', $name, $k, $v) : ($name . '=' . $v);
        } 
        elseif (!is_null($value))
        	$query_string[] = $name . '=' . $value;
        else
        	$query_string[] = $name;
      }
      $query_string = implode('&', $query_string);	        
    } 
    else
    	$query_string = '';

    return $query_string;
  }

  /**
  * Parses raw query_string and returns an array of it
  */
  function _parse_query_string($query_string)
  {
    $query_string = rawurldecode($query_string);
    $parts = preg_split('/&/', $query_string, -1, PREG_SPLIT_NO_EMPTY);

    $return = array();
    
    foreach ($parts as $part) 
    {
      if (strpos($part, '=') !== false) 
      {
        $value = rawurlencode(substr($part, strpos($part, '=') + 1));
        $key   = substr($part, 0, strpos($part, '='));
      } 
      else 
      {
        $value = null;
        $key   = $part;
      }
      
      if (substr($key, -2) == '[]') 
      {
        $key = substr($key, 0, -2);
        if (@!is_array($return[$key])) 
        {
            $return[$key]   = array();
            $return[$key][] = $value;
        } 
        else
        	$return[$key][] = $value;
      } 
      elseif (!$this->_use_brackets && !empty($return[$key])) 
      {
        $return[$key]   = (array)$return[$key];
        $return[$key][] = $value;
      } 
      else
      	$return[$key] = $value;
    }

    return $return;
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