<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class CacheRegistry
{
  protected $cache = array();
  
  protected function _encode_key($key)
  {
    return md5(serialize($key));
  }
  
  public function put($key, $value, $group = 'default')
  {
    $this->cache[$group][$this->_encode_key($key)] = $value; 
  }
  
  public function get($key, $group = 'default')
  {
    $raw_key = $this->_encode_key($key);
    
    if(isset($this->cache[$group][$raw_key]))
      return $this->cache[$group][$raw_key];
    else
      return null;
  }
  
  public function flush($group = null)
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
