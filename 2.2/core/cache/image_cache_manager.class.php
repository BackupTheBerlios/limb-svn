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
  
require_once(LIMB_DIR . '/core/lib/system/fs.class.php');
require_once(LIMB_DIR . '/core/lib/security/user.class.php');

class image_cache_manager
{
  var $id;
  var $uri;
  var $rules = array();
  var $matched_rule;
  var $fetcher;
  
  function image_cache_manager()
  {
  }
  
  function _define_replace_regex_array()
  {
    return array(
        '~(<img[^>]+src=)("|\')?/root\?node_id=(\d+)(&(thumbnail|original|icon))?("|\')?([^<]*>)~',
        '~(background=)("|\')?/root\?node_id=(\d+)(&(thumbnail|original|icon))?("|\')?()~'
      );
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
  
  function process_content(&$content)
  {    
    if(!$this->is_cacheable())
      return false;
    
    $content = $this->_rewrite_images($content);
    
    return true;
  }
  
  function _rewrite_images(&$content)
  {
    if(empty($content))
      return '';
    
    return preg_replace_callback(
      $this->_define_replace_regex_array(),
      array($this, '_replace_callback'),
      $content
    );
  }
  
  function _replace_callback($matches)
  {
    $fetcher =& $this->_get_fetcher();
    
    $object_data = $fetcher->fetch_one_by_node_id((int)$matches[3]);
    
    if(!empty($matches[5]))
      $image = $object_data['variations'][$matches[5]];
    else
      $image = $object_data['variations']['thumbnail'];
        
    $rewritten_path = '/var/images/' . $image['media_id'];
    
    $extension = '';
    switch($image['mime_type'])
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
    
    $this->_cache_media_file($image['media_id'], $extension);
    
    return "{$matches[1]}'{$rewritten_path}{$extension}'{$matches[7]}";
  }
  
  function _cache_media_file($media_id, $extension)
  {
    fs :: mkdir(IMAGE_CACHE_DIR);
    
    if(file_exists(MEDIA_DIR . $media_id . '.media'))
      copy(MEDIA_DIR . $media_id . '.media', IMAGE_CACHE_DIR . $media_id . $extension);
  }
  
  function & _get_fetcher()
  {
    if($this->fetcher)
      return $this->fetcher;
      
    $this->fetcher =& fetcher :: instance();
    return $this->fetcher;
  }
  
  function & _get_user()
  {
    return user :: instance();
  }
   
  function is_cacheable()
  {
    if(!$this->uri)
      return false;
        
    $uri_path = $this->uri->get_path();
    
    $rules =& $this->get_rules();
    
    $user =& $this->_get_user();
    
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
     
  function flush()
  {
    $files = fs :: find_subitems(IMAGE_CACHE_DIR, 'f');

    foreach($files as $file)
    {
      unlink($file);
    }  
  }
  
  function get_cache_size()
  {
    $files = fs :: find_subitems(IMAGE_CACHE_DIR, 'f');
    
    $size = 0;
    
    foreach($files as $file)
    {
      $size += (filesize($file));
    }  
    
    return $size;
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
    
    $ini =& get_ini('image_cache.ini');
    $this->rules = array();
    
    $groups = $ini->get_all();

    foreach($groups as $group => $data)
    {
      if(strpos($group, 'rule') === 0)
        $this->rules[] = $data;
    }
  }
}

?>