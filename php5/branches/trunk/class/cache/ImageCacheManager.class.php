<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
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

require_once(LIMB_DIR . '/class/lib/system/Fs.class.php');

class ImageCacheManager
{
  protected $id;
  protected $uri;
  protected $rules = array();
  protected $matched_rule;
  protected $found_images = array();
  protected $wild_card;

  protected function _defineReplaceRegexArray()
  {
    return array(
        '~(<img[^>]+src=)("|\')?/root\?node_id=(\d+)(&(thumbnail|original|icon))?("|\')?([^<]*>)~',
        '~(background=)("|\')?/root\?node_id=(\d+)(&(thumbnail|original|icon))?("|\')?()~'
      );
  }

  protected function _setMatchedRule($rule)
  {
    $this->matched_rule = $rule;
  }

  protected function _getMatchedRule()
  {
    return $this->matched_rule;
  }

  public function setUri($uri)
  {
    $this->id = null;
    $this->uri = $uri;
  }

  public function processContent(&$content)
  {
    if(!$this->isCacheable())
      return false;

    $content = $this->_replaceImages($content);

    return true;
  }

  protected function _replaceImages($content)
  {
    if(empty($content))
      return '';

    $this->found_images = array();
    $this->wild_card = md5(mt_srand());

    $content = preg_replace_callback(
      $this->_defineReplaceRegexArray(),
      array($this, '_markImagesCallback'),
      $content
    );

    $not_cached_images = $this->_getNotCachedImages();
    $cached_images = $this->_getCachedImages();

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
          $replace[$this->_getWildcardHash($node_id, $variation)] =
            '/root?node_id=' . $node_id . '&' . $variation;

          continue;
        }

        $cache_name = $node_id . $variation . $image['extension'];
        $replace[$this->_getWildcardHash($node_id, $variation)] = IMAGE_CACHE_WEB_DIR . $cache_name;
      }
    }

    if($replace)
      return strtr($content, $replace);
    else
      return $content;
  }

  protected function _getWildcardHash($node_id, $variation)
  {
    return "<{$this->wild_card}{$node_id}-{$variation}{$this->wild_card}>";
  }

  protected function _getNotCachedImages()
  {
    $node_ids = array();
    foreach($this->found_images as $node_id => $variations)
    {
      foreach(array_keys($variations) as $variation)
      {
        if(!$this->_isImageCached($node_id, $variation))
          $node_ids[$node_id] = 1;
      }
    }

    $datasource = Limb :: toolkit()->getDatasource('SiteObjectsByNodeIdsDatasource');
    $datasource->setNodeIds(array_keys($node_ids));
    $datasource->setSiteObjectClassName('ImageObject');

    $images = $datasource->fetch();

    $result = array();
    foreach($images as $node_id => $image)
    {
      $variations = $this->found_images[$node_id];
      foreach(array_keys($variations) as $variation)
      {
        $variation_data = $image['variations'][$variation];

        $extension = $this->_getMimeExtension($variation_data['mime_type']);
        $result[$node_id] = array(
          'variation' => $variation,
          'extension' => $extension
         );

        $cache_name = $node_id . $variation . $extension;
        $this->_cacheMediaFile($variation_data['media_id'], $cache_name);
      }
    }

    return $result;
  }

  protected function _getCachedImages()
  {
    $result = array();
    foreach($this->found_images as $node_id => $variations)
    {
      foreach(array_keys($variations) as $variation)
      {
        if($extension = $this->_getCachedImageExtension($node_id, $variation))
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

  protected function _isImageCached($node_id, $variation)
  {
    return ($this->_getCachedImageExtension($node_id, $variation) !== false);
  }

  protected function _getCachedImageExtension($node_id, $variation)
  {
    $cache = $node_id . '-' . $variation;

    foreach(array('.jpg', '.gif', '.png') as $extension)
    {
      if(file_exists($cache . $extension))
        return $extension;
    }

    return false;
  }

  protected function _getMimeExtension($mime_type)
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

  protected function _markImagesCallback($matches)
  {
    if(!empty($matches[5]))
      $variation = $matches[5];
    else
      $variation = 'thumbnail';

    $this->found_images[$matches[3]][$variation] = 1;

    return $matches[1] . "'" . $this->_getWildcardHash($matches[3], $variation) . "'" . $matches[7];
  }

  protected function _cacheMediaFile($media_id, $cache_name)
  {
    Fs :: mkdir(IMAGE_CACHE_DIR);

    if(file_exists(MEDIA_DIR . $media_id . '.media') &&  !file_exists(IMAGE_CACHE_DIR . $cache_name))
      copy(MEDIA_DIR . $media_id . '.media', IMAGE_CACHE_DIR . $cache_name);
  }

  public function isCacheable()
  {
    if(!$this->uri)
      return false;

    $uri_path = $this->uri->getPath();

    $rules = $this->getRules();

    foreach($rules as $rule)
    {
      if(!preg_match($rule['path_regex'], $uri_path))
        continue;

      if(isset($rule['groups']))
      {
        if(!$this->_isUserInGroups($rule['groups']))
          continue;
      }

      if(!isset($rule['type']) ||  $rule['type'] === 'allow')
      {
        $this->_setMatchedRule($rule);
        return true;
      }
      else
        return false;
    }

    return false;
  }

  protected function _isUserInGroups($groups)
  {
    $user = Limb :: toolkit()->getUser();

    foreach	($user->get('groups', array()) as $group_name)
      if (in_array($group_name, $groups))
        return true;

    return false;
  }

  public function flush()
  {
    Fs :: mkdir(IMAGE_CACHE_DIR);

    $files = Fs :: findSubitems(IMAGE_CACHE_DIR, 'f');

    foreach($files as $file)
    {
      unlink($file);
    }
  }

  public function getCacheSize()
  {
    Fs :: mkdir(IMAGE_CACHE_DIR);

    $files = Fs :: findSubitems(IMAGE_CACHE_DIR, 'f');

    $size = 0;

    foreach($files as $file)
    {
      $size += (filesize($file));
    }

    return $size;
  }

  public function getRules()
  {
    if(!$this->rules)
      $this->_loadRules();

    return $this->rules;
  }

  protected function _loadRules()
  {
    $this->rules = array();

    $groups = Limb :: toolkit()->getINI('image_cache.ini')->getAll();

    foreach($groups as $group => $data)
    {
      if(strpos($group, 'rule') === 0)
        $this->rules[] = $data;
    }
  }
}

?>