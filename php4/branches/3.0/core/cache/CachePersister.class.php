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

class CachePersister
{
  var $cache = array();

  function put($key, &$value, $group = 'default')
  {
    $this->cache[$group][$key] =& $value;
  }

  function assign(&$variable, $key, $group = 'default')
  {
    if(isset($this->cache[$group]) &&
       array_key_exists($key, $this->cache[$group]))
    {
      $variable = $this->cache[$group][$key];
      return true;
    }

    return false;
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
