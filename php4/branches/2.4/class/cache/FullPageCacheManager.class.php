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
@define('PAGE_CACHE_DIR', VAR_DIR . '/pages/');

require_once(LIMB_DIR . '/class/lib/system/Fs.class.php');

class FullPageCacheManager
{
  var $id;
  var $request;
  var $rules = array();
  var $matched_rule;

  function _setMatchedRule($rule)
  {
    $this->matched_rule = $rule;
  }

  function _getMatchedRule()
  {
    return $this->matched_rule;
  }

  function setRequest(&$request)
  {
    $this->id = null;
    $this->request =& $request;
  }

  function get()
  {
    if(!$this->request)
      return false;

    if($this->isCacheable())
    {
      if($this->cacheExists())
        return $this->readCache();
    }

    return false;
  }

  function write($content)
  {
    if(!$id = $this->getCacheId())
      return false;

    Fs :: mkdir(PAGE_CACHE_DIR);

    $tmp = tempnam(PAGE_CACHE_DIR, '_');
    $f = fopen($tmp, 'w');
    fwrite($f, $content);
    fclose($f);

    if(file_exists(PAGE_CACHE_DIR . $id))
      unlink(PAGE_CACHE_DIR . $id);

    return rename($tmp,  PAGE_CACHE_DIR . $id);
  }

  function getCacheId()
  {
    if(!$this->request)
      return null;

    if(is_null($matched_rule = $this->_getMatchedRule()))
      return null;

    if($this->id)
      return $this->id;

    $query_items = $this->request->export();
    $cache_query_items = array();
    $attributes = array();

    if(isset($matched_rule['optional']) &&  is_array($matched_rule['optional']))
      $attributes = $matched_rule['optional'];

    if(isset($matched_rule['required']) &&  is_array($matched_rule['required']))
      $attributes = array_merge($matched_rule['required'], $attributes);

    foreach($query_items as $key => $value)
    {
      if(in_array($key, $attributes))
        $cache_query_items[$key] = $value;
    }

    ksort($cache_query_items);

    $uri =& $this->request->getUri();
    $this->id = 'f_' . md5($uri->getPath() . serialize($cache_query_items));

    return $this->id;
  }

  function isCacheable()
  {
    if(!$this->request)
      return false;

    $query_items = $this->request->export();
    $query_keys = array_keys($query_items);

    $uri =& $this->request->getUri();
    $uri_path = $uri->getPath();

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

  function _isUserInGroups($groups)
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    foreach	($user->get('groups', array()) as $group_name)
      if (in_array($group_name, $groups))
        return true;

    return false;
  }

  function cacheExists()
  {
    if(!$id = $this->getCacheId())
      return false;

    return file_exists(PAGE_CACHE_DIR . $id);
  }

  function flush()
  {
    Fs :: mkdir(PAGE_CACHE_DIR);

    $files = Fs :: findSubitems(PAGE_CACHE_DIR, 'f', '~^[^f]~');

    foreach($files as $file)
    {
      unlink($file);
    }
  }

  function getCacheSize()
  {
    Fs :: mkdir(PAGE_CACHE_DIR);

    $files = Fs :: findSubitems(PAGE_CACHE_DIR, 'f', '~^[^f]~');

    $size = 0;

    foreach($files as $file)
    {
      $size += (filesize($file));
    }

    return $size;
  }

  function readCache()
  {
    if(!$id = $this->getCacheId())
      return false;

    return file_get_contents(PAGE_CACHE_DIR . $id);
  }

  function getRules()
  {
    if(!$this->rules)
      $this->_loadRules();

    return $this->rules;
  }

  function _loadRules()
  {
    $this->rules = array();

    $toolkit =& Limb :: toolkit();
    $ini =& $toolkit->getINI('full_page_cache.ini');
    $groups = $ini->getAll();

    foreach($groups as $group => $data)
    {
      if(strpos($group, 'rule') === 0)
        $this->rules[] = $data;
    }
  }
}

?>