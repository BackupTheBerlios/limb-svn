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
if(!defined('PAGE_CACHE_DIR'))
  define('PAGE_CACHE_DIR', VAR_DIR . 'pages/');
  
require_once(LIMB_DIR . '/core/lib/system/dir.class.php');
require_once(LIMB_DIR . '/core/lib/security/user.class.php');

class full_page_cache_manager
{
  var $id;
  var $uri;
  var $rules = array();
  var $matched_rule;
  
  function full_page_cache_manager()
  {
  }
    
  function _set_matched_rule($rule)
  {
    $this->matched_rule = $rule;
  }
  
  function _get_matched_rule()
  {
    return $this->matched_rule;
  }
  
  function set_uri(&$uri)
  {
    $this->id = null;
    $this->uri =& $uri;
  }
  
  function & get()
  {
    if(!$this->uri)
      return false;
    
    if($this->is_cacheable())
    {
      if($this->cache_exists())
        return $this->read_cache();
    }
    
    return false;
  }
  
  function & _get_user()
  {
    return user :: instance();
  }
  
  function write(&$content)
  {      
    if(!$id = $this->get_cache_id())
      return false;      
    
    dir :: mkdir(PAGE_CACHE_DIR);
    
    $tmp = tempnam(PAGE_CACHE_DIR, '_');
    $f = fopen($tmp, 'w');
    fwrite($f, $content);
    fclose($f);
    
    if(file_exists(PAGE_CACHE_DIR . $id))
      unlink(PAGE_CACHE_DIR . $id);
    
    return rename($tmp,  PAGE_CACHE_DIR . $id);
  }
  
  function get_cache_id()
  {
    if(!$this->uri)
      return null;

    if(is_null($matched_rule = $this->_get_matched_rule()))
      return null;
    
    if($this->id)
      return $this->id;
              
    $query_items = $this->uri->get_query_items();
    $cache_query_items = array();
          
    if(isset($matched_rule['attributes']) && is_array($matched_rule['attributes']))
    {
      foreach($query_items as $key => $value)
      {
        if(in_array($key, $matched_rule['attributes']))
          $cache_query_items[$key] = $value;
      }    
    } 
    else
    {
      $cache_query_items = array();
    }
    
    $this->id = md5($this->uri->get_path() . serialize($cache_query_items));
    
    return $this->id;
  }
  
  function is_cacheable()
  {
    if(!$this->uri)
      return false;
    
    $query_items = $this->uri->get_query_items();
    $query_keys = array_keys($query_items);
    
    $uri_path = $this->uri->get_path();
    
    $rules =& $this->get_rules();
    
    $user =& $this->_get_user();
    
    foreach($rules as $rule)
    {
      if(isset($rule['groups']))
      {
        if(!$user->is_in_groups($rule['groups']))
          continue;
      }
      
      if(isset($rule['attributes']))
      {
        if(sizeof($query_keys) < sizeof($rule['attributes']))
          continue;
        
        foreach($rule['attributes'] as $query_key)
        {
          if(!in_array($query_key, $query_keys))
            continue 2;
        }
      }
      
      if(preg_match($rule['path_regex'], $uri_path))
      {
        if(!isset($rule['type']) || $rule['type'] === 'allow')
        {
          $this->_set_matched_rule($rule);
          return true;
        }  
        else
          return false;
      }
    }
    
    return false;
  }
    
  function cache_exists()
  { 
    if(!$id = $this->get_cache_id())
      return false;
  
    return file_exists(PAGE_CACHE_DIR . $id);
  }
  
  function read_cache()
  {
    if(!$id = $this->get_cache_id())
      return false;
  
    return file_get_contents(PAGE_CACHE_DIR . $id);
  }
          
  function get_rules()
  {
    if(!$this->rules)
      $this->_load_rules();
    
    return $this->rules;
  }
  
  function _load_rules()
  {
    include_once(LIMB_DIR . '/core/lib/util/ini.class.php');
    
    $ini =& get_ini('full_page_cache.ini');
    $this->rules = array();
    
    $groups = $ini->get_named_array();

    foreach($groups as $group => $data)
    {
      if(strpos($group, 'rule') === 0)
        $this->rules[] = $data;
    }
  }
}

?>