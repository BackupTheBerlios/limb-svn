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

class cache_registry
{
  var $cache = array();

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
  }

  function & get($raw_key, $group = 'default')
  {
    $key = $this->_normalize_key($raw_key);

    if(isset($this->cache[$group][$key]))
      return $this->cache[$group][$key];
    else
      return null;
  }

  function purge($raw_key, $group = 'default')
  {
    $key = $this->_normalize_key($raw_key);

    if(isset($this->cache[$group][$key]))
      unset($this->cache[$group][$key]);
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
    }
    else
    {
      $this->cache = array();
    }
  }
}
?>
