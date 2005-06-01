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
require_once(dirname(__FILE__) . '/CachePersister.class.php');
require_once(LIMB_DIR . '/core/system/Fs.class.php');

@define('CACHE_FILE_PREFIX', 'cache_');

class CacheFilePersister extends CachePersister
{
  var $cache_dir;

  function CacheFilePersister($id = 'cache')
  {
    parent :: CachePersister($id);

    $this->cache_dir = VAR_DIR . '/' . $id;

    Fs :: mkdir($this->cache_dir);
  }

  function getCacheDir()
  {
    return $this->cache_dir;
  }

  function put($key, &$value, $group = 'default')
  {
    $file = $this->_getCacheFilePath($group, $key);
    Fs :: safeWrite($file, $this->_makePhpContent($value));
  }

  function assign(&$variable, $key, $group = 'default')
  {
    $file = $this->_getCacheFilePath($group, $key);

    if(!file_exists($file))
      return false;

    include($file);
    $variable = unserialize($value);

    return true;
  }

  function flushValue($key, $group = 'default')
  {
    $this->_removeFileCache($group, $key);
  }

  function flushGroup($group)
  {
    $this->_removeFileCache($group);
  }

  function flushAll()
  {
    $this->_removeFileCache();
  }

  function _makePhpContent($value)
  {
    return "<?php\n\$value = " . var_export(serialize($value), true) . ";\n?>";
  }

  function _removeFileCache($group = false, $key = false)
  {
    if($key)
    {
      @unlink($this->_getCacheFilePath($group, $key));
    }
    else
    {
      $files = Fs :: find($this->cache_dir, 'f', '~^' . preg_quote($this->_getCacheFilePrefix($group)) . '~');
      foreach($files as $file)
        @unlink($file);
    }
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
    return $this->cache_dir . '/' . $this->_getCacheFileName($group, $key);
  }
}
?>
