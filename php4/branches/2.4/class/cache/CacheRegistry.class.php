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

  function _encodeKey($key)
  {
    return md5(serialize($key));
  }

  function put($key, $value, $group = 'default')
  {
    $this->cache[$group][$this->_encodeKey($key)] = $value;
  }

  function get($key, $group = 'default')
  {
    $raw_key = $this->_encodeKey($key);

    if(isset($this->cache[$group][$raw_key]))
      return $this->cache[$group][$raw_key];
    else
      return null;
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
