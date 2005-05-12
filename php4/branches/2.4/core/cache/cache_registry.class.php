<?php
/**********************************************************************************
* copyright 2004 BIT, _ltd. http://limb-project.com, mailto: support@limb-project.com
*
* released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: cache_registry.class.php 1260 2005-04-20 15:10:07Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/system/fs.class.php');

@define('CACHE_DIR', VAR_DIR . '/cache');
@define('CACHE_FILE_PREFIX', 'cache_');

class cache_registry
{
  var $session_id;
  var $cache = array();

  function cache_registry()
  {
    $this->session_id = session_id();
    fs :: mkdir(CACHE_DIR);
  }

  function _normalize_key($key)
  {
    if(is_scalar($key))
      return $key;
    else
      return md5(serialize($key));
  }

  function put($raw_key, &$value, $group = 'default')
  {
    $key = $this->_normalize_key($raw_key);

    $this->cache[$group][$key] =& $value;

    $file = $this->_get_cache_file_path($group, $key);

    fs :: safe_write($file, $this->_make_php_content($value));
  }

  function _make_php_content($value)
  {
    return "<?php\n\$value = " . var_export($value, true) . ";\n?>";
  }

  function assign(&$variable, $raw_key, $group = 'default')
  {
    $key = $this->_normalize_key($raw_key);

    if(isset($this->cache[$group][$key]))
    {
      $variable = $this->cache[$group][$key];
      return true;
    }
    else
    {
      $file = $this->_get_cache_file_path($group, $key);

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

  function purge($raw_key, $group = 'default')
  {
    $key = $this->_normalize_key($raw_key);

    if(isset($this->cache[$group][$key]))
      unset($this->cache[$group][$key]);

    $this->_remove_file_cache($group, $key);
  }

  function purge_group($group = null)
  {
    $this->flush($group);
  }

  function flush($group = null)
  {
    if($group !== null)
    {
      if(isset($this->cache[$group]))
        $this->cache[$group] = array();

      $this->_remove_file_cache($group);
    }
    else
    {
      $this->_remove_file_cache();
      $this->cache = array();
    }
  }

  function _remove_file_cache($group = false, $key = false)
  {
    if($key)
    {
      @unlink($this->_get_cache_file_path($group, $key));
    }
    else
    {
      $files = fs :: find(CACHE_DIR, 'f', '~^' . preg_quote($this->_get_cache_file_prefix($group)) . '~');
      foreach($files as $file)
        @unlink($file);
    }
  }

  function _get_cache_file_prefix($group = false)
  {
    return CACHE_FILE_PREFIX . ($group ? $group : '');
  }

  function _get_cache_file_name($group, $key)
  {
    return $this->_get_cache_file_prefix($group) . '_' . $key . '.php';
  }

  function _get_cache_file_path($group, $key)
  {
    return CACHE_DIR . '/' . $this->_get_cache_file_name($group, $key);
  }
}
?>
