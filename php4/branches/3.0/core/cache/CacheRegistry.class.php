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

  function put($key, &$value, $group = 'default')
  {
    $this->cache[$group][$key] =& $value;
  }

  function & get($key, $group = 'default')
  {
    if(isset($this->cache[$group][$key]))
      return $this->cache[$group][$key];
    else
      return null;
  }

  function purge($key, $group = 'default')
  {
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
