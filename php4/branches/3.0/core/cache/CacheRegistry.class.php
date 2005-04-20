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

class CacheRegistry
{
  var $cache = array();

  function _normalizeKey($key)
  {
    if(is_scalar($key))
      return $key;
    else
      return md5(serialize($key));
  }

  function put($raw_key, &$value, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);

    $this->cache[$group][$key] =& $value;
  }

  function & get($raw_key, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);

    if(isset($this->cache[$group][$key]))
      return $this->cache[$group][$key];
    else
      return null;
  }

  function purge($raw_key, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);

    if(isset($this->cache[$group][$key]))
      unset($this->cache[$group][$key]);
  }

  function purgeGroup($group = null)
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
