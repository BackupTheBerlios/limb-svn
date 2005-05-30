<?php
/**********************************************************************************
* copyright 2004 BIT, _ltd. http://limb-project.com, mailto: support@limb-project.com
*
* released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/system/Fs.class.php');

@define('CACHE_DIR', VAR_DIR . '/cache');
@define('CACHE_FILE_PREFIX', 'cache_');

class CacheRegistry
{
  var $session_id;
  var $cache = array();

  function CacheRegistry()
  {
    $this->session_id = session_id();
    Fs :: mkdir(CACHE_DIR);
  }

  function put($raw_key, &$value, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);

    $this->cache[$group][$key] =& $value;

    $file = $this->_getCacheFilePath($group, $key);

    Fs :: safeWrite($file, $this->_makePhpContent($value));
  }

  function assign(&$variable, $raw_key, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);

    if(isset($this->cache[$group][$key]))
    {
      $variable = $this->cache[$group][$key];
      return true;
    }
    else
    {
      $file = $this->_getCacheFilePath($group, $key);

      if(!file_exists($file))
        return false;

      include($file);

      $this->cache[$group][$key] = $value;
      $variable = $value;
      return true;
    }
  }

  function & get($raw_key, $group = 'default')
  {
    if($this->assign($value, $raw_key, $group))
      return $value;
    else
      return null;
  }

  function flushValue($raw_key, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);

    if(isset($this->cache[$group][$key]))
      unset($this->cache[$group][$key]);

    $this->_removeFileCache($group, $key);
  }

  function flushGroup($group)
  {
    if(isset($this->cache[$group]))
      $this->cache[$group] = array();

    $this->_removeFileCache($group);
  }

  function flushAll()
  {
    $this->cache = array();
    $this->_removeFileCache();
  }

  function _makePhpContent($value)
  {
    return "<?php\n\$value = " . var_export($value, true) . ";\n?>";
  }

  function _removeFileCache($group = false, $key = false)
  {
    if($key)
    {
      @unlink($this->_getCacheFilePath($group, $key));
    }
    else
    {
      $files = Fs :: find(CACHE_DIR, 'f', '~^' . preg_quote($this->_getCacheFilePrefix($group)) . '~');
      foreach($files as $file)
        @unlink($file);
    }
  }

  function _normalizeKey($key)
  {
    if(is_scalar($key))
      return $key;
    else
      return md5(serialize($key));
  }

  function _getCacheFilePrefix($group = false)
  {
    return CACHE_FILE_PREFIX . ($group ? $group : '');
  }

  function _getCacheFileName($group, $key)
  {
    return $this->_getCacheFilePrefix($group) . '_' . $key . '.php';
  }

  function _getCacheFilePath($group, $key)
  {
    return CACHE_DIR . '/' . $this->_getCacheFileName($group, $key);
  }
}
?>
