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
if(!defined('PAGE_CACHE_DIR'))
  define('PAGE_CACHE_DIR', VAR_DIR . 'pages/');
  
require_once(LIMB_DIR . '/class/lib/system/fs.class.php');

class full_page_cache_manager
{
  protected $id;
  protected $uri;
  protected $rules = array();
  protected $matched_rule;
  
  protected function _set_matched_rule($rule)
  {
    $this->matched_rule = $rule;
  }
  
  protected function _get_matched_rule()
  {
    return $this->matched_rule;
  }
  
  public function set_uri($uri)
  {
    $this->id = null;
    $this->uri = $uri;
  }
  
  public function get()
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
    
  public function write($content)
  {      
    if(!$id = $this->get_cache_id())
      return false;      
    
    fs :: mkdir(PAGE_CACHE_DIR);
    
    $tmp = tempnam(PAGE_CACHE_DIR, '_');
    $f = fopen($tmp, 'w');
    fwrite($f, $content);
    fclose($f);
    
    if(file_exists(PAGE_CACHE_DIR . $id))
      unlink(PAGE_CACHE_DIR . $id);
    
    return rename($tmp,  PAGE_CACHE_DIR . $id);
  }
  
  public function get_cache_id()
  {
    if(!$this->uri)
      return null;

    if(is_null($matched_rule = $this->_get_matched_rule()))
      return null;
    
    if($this->id)
      return $this->id;
              
    $query_items = $this->uri->get_query_items();
    $cache_query_items = array();
    $attributes = array();
    
    if(isset($matched_rule['optional']) && is_array($matched_rule['optional']))
      $attributes = $matched_rule['optional'];

    if(isset($matched_rule['required']) && is_array($matched_rule['required']))
      $attributes = array_merge($matched_rule['required'], $attributes);
    
    foreach($query_items as $key => $value)
    {          
      if(in_array($key, $attributes))
        $cache_query_items[$key] = $value;      
    }
    
    ksort($cache_query_items);
    
    $this->id = 'f_' . md5($this->uri->get_path() . serialize($cache_query_items));
    
    return $this->id;
  }
  
  public function is_cacheable()
  {
    if(!$this->uri)
      return false;
    
    $query_items = $this->uri->get_query_items();
    $query_keys = array_keys($query_items);
    
    $uri_path = $this->uri->get_path();
    
    $rules = $this->get_rules();
    
    foreach($rules as $rule)
    {
      if(!preg_match($rule['path_regex'], $uri_path))
        continue;
    
      if(isset($rule['groups']))
      {
        if(!$this->_is_user_in_groups($rule['groups']))
          continue;
      }
      
      if(isset($rule['required']))
      {
        if(sizeof($query_keys) < sizeof($rule['required']))
          continue;
        
        foreach($rule['required'] as $query_key)
        {
          if(!in_array($query_key, $query_keys))
            continue 2;
        }
      }
              
      if(!isset($rule['type']) || $rule['type'] === 'allow')
      {
        $this->_set_matched_rule($rule);
        return true;
      }  
      else
        return false;
    }
    
    return false;
  }
  
  protected function _is_user_in_groups($groups)
  {
    $user = Limb :: toolkit()->getUser();
    
		foreach	($user->get('groups', array()) as $group_name)
			if (in_array($group_name, $groups))
				return true; 
		
		return false;    
  }
    
  public function cache_exists()
  { 
    if(!$id = $this->get_cache_id())
      return false;
  
    return file_exists(PAGE_CACHE_DIR . $id);
  }
  
  public function flush()
  {
    fs :: mkdir(PAGE_CACHE_DIR);
  
    $files = fs :: find_subitems(PAGE_CACHE_DIR, 'f', '~^[^f]~');

    foreach($files as $file)
    {
      unlink($file);
    }  
  }
  
  public function get_cache_size()
  {
    fs :: mkdir(PAGE_CACHE_DIR);

    $files = fs :: find_subitems(PAGE_CACHE_DIR, 'f', '~^[^f]~');
    
    $size = 0;
    
    foreach($files as $file)
    {
      $size += (filesize($file));
    }  
    
    return $size;
  }
  
  public function read_cache()
  {
    if(!$id = $this->get_cache_id())
      return false;
  
    return file_get_contents(PAGE_CACHE_DIR . $id);
  }
          
  public function get_rules()
  {
    if(!$this->rules)
      $this->_load_rules();
    
    return $this->rules;
  }
  
  protected function _load_rules()
  {
    include_once(LIMB_DIR . '/class/lib/util/ini.class.php');
    
    $this->rules = array();
    
    $groups = get_ini('full_page_cache.ini')->get_all();

    foreach($groups as $group => $data)
    {
      if(strpos($group, 'rule') === 0)
        $this->rules[] = $data;
    }
  }
}

?>