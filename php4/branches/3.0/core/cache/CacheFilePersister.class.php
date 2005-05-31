<?php
/**********************************************************************************
* copyright 2004 BIT, _ltd. http://limb-project.com, mailto: support@limb-project.com
*
* released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CacheRegistry.class.php 1336 2005-05-30 12:54:56Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/system/Fs.class.php');

@define('CACHE_DIR', VAR_DIR . '/cache');
@define('CACHE_FILE_PREFIX', 'cache_');

class CacheFilePersister
{
  function CacheFilePersister()
  {
    Fs :: mkdir(CACHE_DIR);
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
      $files = Fs :: find(CACHE_DIR, 'f', '~^' . preg_quote($this->_getCacheFilePrefix($group)) . '~');
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
    return CACHE_DIR . '/' . $this->_getCacheFileName($group, $key);
  }
}
?>
