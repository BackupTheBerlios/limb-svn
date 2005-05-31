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

class CacheRegistry
{
  var $persister = null;

  function CacheRegistry()
  {
    $this->persister =& $this->_createCachePersister();
  }

  function & _createCachePersister()
  {
    include_once(dirname(__FILE__) . '/CachePersister.class.php');
    return new CachePersister();
  }

  function setCachePersister(&$persister)
  {
    $this->persister =& $persister;
  }

  function put($raw_key, &$value, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);
    $this->persister->put($key, $value, $group);
  }

  function assign(&$variable, $raw_key, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);
    return $this->persister->assign($variable, $key, $group);
  }

  function flushValue($raw_key, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);
    $this->persister->flushValue($key, $group);
  }

  function flushGroup($group)
  {
    $this->persister->flushGroup($group);
  }

  function flushAll()
  {
    $this->persister->flushAll();
  }

  function _normalizeKey($key)
  {
    if(is_scalar($key))
      return $key;
    else
      return md5(serialize($key));
  }
}
?>
