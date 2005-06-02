<?php
/**********************************************************************************
* copyright 2004 BIT, _ltd. http://limb-project.com, mailto: support@limb-project.com
*
* released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CachePersister.class.php 1343 2005-06-01 08:16:13Z pachanga $
*
***********************************************************************************/
require_once(dirname(__FILE__) . '/CachePersister.class.php');

class CacheMemoryPersister extends CachePersister
{
  var $cache = array();

  function put($key, &$value, $group = 'default')
  {
    $this->cache[$group][$key] =& $value;
  }

  function & get($key, $group = 'default')
  {
    if(isset($this->cache[$group]) &&
       array_key_exists($key, $this->cache[$group]))
    {
      return $this->cache[$group][$key];
    }

    return CACHE_NULL_RESULT;
  }

  function flushValue($key, $group = 'default')
  {
    if(isset($this->cache[$group][$key]))
      unset($this->cache[$group][$key]);
  }

  function flushGroup($group)
  {
    if(isset($this->cache[$group]))
      $this->cache[$group] = array();
  }

  function flushAll()
  {
    $this->cache = array();
  }
}
?>
