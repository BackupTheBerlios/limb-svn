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
if(!defined('IMAGE_CACHE_DIR'))
  define('IMAGE_CACHE_DIR', VAR_DIR . 'images/');
  
if(!defined('IMAGE_CACHE_WEB_DIR'))
  define('IMAGE_CACHE_WEB_DIR', '/var/images/');
  
require_once(LIMB_DIR . '/class/lib/system/fs.class.php');
require_once(LIMB_DIR . '/class/core/user.class.php');

class image_cache_manager
{
  protected $id;
  protected $uri;
  private   $rules = array();
  protected $matched_rule;
  private   $fetcher;
  protected $found_images = array();
  protected $wild_card;
    
  protected function _define_replace_regex_array()
  {
    return array(
        '~(<img[^>]+src=)("|\')?/root\?node_id=(\d+)(&(thumbnail|original|icon))?("|\')?([^<]*>)~',
        '~(background=)("|\')?/root\?node_id=(\d+)(&(thumbnail|original|icon))?("|\')?()~'
      );
  }
    
  protected function _set_matched_rule($rule)
  {
    $this->matched_rule = $rule;
  }
  
  protected function _get_matched_rule()
  {
    return $this->matched_rule;
  }
  
  public function set_uri(/*uri*/$uri)
  {
    $this->id = null;
    $this->uri = $uri;
  }
  
  public function process_content(&$content)
  { 
    if(!$this->is_cacheable())
      return false;
    
    $content = $this->_replace_images($content);    
    
    return true;
  }
  
  protected function _replace_images($content)
  {
    if(empty($content))
      return '';
    
    $this->found_images = array();
    $this->wild_card = md5(mt_srand());
    
    $content = preg_replace_callback(
      $this->_define_replace_regex_array(),
      array($this, '_mark_images_callback'),
      $content
    );
    
    $not_cached_images = $this->_get_not_cached_images();
    $cached_images = $this->_get_cached_images();
    
    $images = array_merge($cached_images, $not_cached_images);
    
    $replace = array();
    foreach($this->found_images as $node_id => $variations)
    {
      foreach(array_keys($variations) as $variation)
      {
        if (isset($cached_images[$node_id]))
          $image = $cached_images[$node_id];
        elseif(isset($not_cached_images[$node_id]))  
          $image = $not_cached_images[$node_id];
        else
        {        
          $replace[$this->_get_wildcard_hash($node_id, $variation)] = 
            '/root?node_id=' . $node_id . '&' . $variation;
            
          continue;
        }
  
        $cache_name = $node_id . $variation . $image['extension'];
        $replace[$this->_get_wildcard_hash($node_id, $variation)] = IMAGE_CACHE_WEB_DIR . $cache_name;
      }  
    }
        
    if($replace)
      return strtr($content, $replace);
    else
      return $content;
  }
    
  protected function _get_wildcard_hash($node_id, $variation)
  {
    return "<{$this->wild_card}{$node_id}-{$variation}{$this->wild_card}>";
  }
  
  protected function _get_not_cached_images()
  {
    $node_ids = array();
    foreach($this->found_images as $node_id => $variations)
    {
      foreach(array_keys($variations) as $variation)
      {
        if(!$this->_is_image_cached($node_id, $variation))
          $node_ids[$node_id] = 1;
      }    
    }
    
    $fetcher = $this->_get_fetcher();
    $images = $fetcher->fetch_by_node_ids(array_keys($node_ids), 'image_object', $counter = 0);
    
    $result = array();
    foreach($images as $node_id => $image)
    { 
      $variations = $this->found_images[$node_id];
      foreach(array_keys($variations) as $variation)
      {
        $variation_data = $image['variations'][$variation];
        
        $extension = $this->_get_mime_extension($variation_data['mime_type']);
        $result[$node_id] = array(
          'variation' => $variation,
          'extension' => $extension
         );
  
        $cache_name = $node_id . $variation . $extension;
        $this->_cache_media_file($variation_data['media_id'], $cache_name);    
      }  
    }
    
    return $result;
  }

  protected function _get_cached_images()
  {
    $result = array();
    foreach($this->found_images as $node_id => $variations)
    {
      foreach(array_keys($variations) as $variation)
      {
        if($extension = $this->_get_cached_image_extension($node_id, $variation))
        {
          $result[$node_id] = array(
            'variation' => $variation,
            'extension' => $extension
           );
        }
      }  
    }
    
    return $result;
  }
  
  protected function _is_image_cached($node_id, $variation)
  {
    return ($this->_get_cached_image_extension($node_id, $variation) !== false);
  }
  
  protected function _get_cached_image_extension($node_id, $variation)
  {
    $cache = $node_id . '-' . $variation;
    
    foreach(array('.jpg', '.gif', '.png') as $extension)
    {
      if(file_exists($cache . $extension))
        return $extension;
    }
    
    return false;
  }
  
  protected function _get_mime_extension($mime_type)
  {
    $extension = '';
    switch($mime_type)
    {
      case 'image/jpeg':
      case 'image/jpg':
      case 'image/pjpeg':
        $extension .= '.jpg';
        break;
      case 'image/png':
        $extension .= '.png';
        break;
      case 'image/gif':
        $extension .= '.gif';
        break;      
    }
    
    return $extension;  
  }
  
  protected function _mark_images_callback($matches)
  {
    if(!empty($matches[5]))
      $variation = $matches[5];
    else
      $variation = 'thumbnail';
    
    $this->found_images[$matches[3]][$variation] = 1;
    
    return $matches[1] . "'" . $this->_get_wildcard_hash($matches[3], $variation) . "'" . $matches[7];
  }
  
  protected function _cache_media_file($media_id, $cache_name)
  {
    fs :: mkdir(IMAGE_CACHE_DIR);
    
    if(file_exists(MEDIA_DIR . $media_id . '.media') && !file_exists(IMAGE_CACHE_DIR . $cache_name))
      copy(MEDIA_DIR . $media_id . '.media', IMAGE_CACHE_DIR . $cache_name);
  }
  
  protected function _get_fetcher()
  {
    if($this->fetcher)
      return $this->fetcher;
      
    $this->fetcher = fetcher :: instance();
    return $this->fetcher;
  }
  
  protected function _get_user()
  {
    return user :: instance();
  }
   
  public function is_cacheable()
  {
    if(!$this->uri)
      return false;
        
    $uri_path = $this->uri->get_path();
    
    $rules = $this->get_rules();
    
    $user = $this->_get_user();
    
    foreach($rules as $rule)
    {
      if(!preg_match($rule['path_regex'], $uri_path))
        continue;
    
      if(isset($rule['groups']))
      {
        if(!$user->is_in_groups($rule['groups']))
          continue;
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
     
  public function flush()
  {
    fs :: mkdir(IMAGE_CACHE_DIR);
  
    $files = fs :: find_subitems(IMAGE_CACHE_DIR, 'f');

    foreach($files as $file)
    {
      unlink($file);
    }  
  }
  
  public function get_cache_size()
  {
    fs :: mkdir(IMAGE_CACHE_DIR);
  
    $files = fs :: find_subitems(IMAGE_CACHE_DIR, 'f');
    
    $size = 0;
    
    foreach($files as $file)
    {
      $size += (filesize($file));
    }  
    
    return $size;
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
    
    $groups = get_ini('image_cache.ini')->get_all();

    foreach($groups as $group => $data)
    {
      if(strpos($group, 'rule') === 0)
        $this->rules[] = $data;
    }
  }
}

?>