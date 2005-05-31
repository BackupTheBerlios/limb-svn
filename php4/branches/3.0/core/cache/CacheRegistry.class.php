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
  var $session_id;
  var $cache = array();
  var $persister = null;

  function CacheRegistry()
  {
    $this->session_id = session_id();
    $this->persister =& $this->_createCachePersister();
  }

  function & _createCachePersister()
  {
    include_once(dirname(__FILE__) . '/CacheFilePersister.class.php');
    return new CacheFilePersister();
  }

  function setCachePersister(&$persister)
  {
    $this->persister =& $persister;
  }

  function put($raw_key, &$value, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);

    $this->cache[$group][$key] =& $value;

    $this->persister->put($key, $value, $group);
  }

  function assign(&$variable, $raw_key, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);

    if(isset($this->cache[$group]) &&
       array_key_exists($key, $this->cache[$group]))
    {
      $variable = $this->cache[$group][$key];
      return true;
    }
    else
    {
      return $this->persister->assign($variable, $key, $group);
    }
  }

  function flushValue($raw_key, $group = 'default')
  {
    $key = $this->_normalizeKey($raw_key);

    if(isset($this->cache[$group][$key]))
      unset($this->cache[$group][$key]);

    $this->persister->flushValue($key, $group);
  }

  function flushGroup($group)
  {
    if(isset($this->cache[$group]))
      $this->cache[$group] = array();

    $this->persister->flushGroup($group);
  }

  function flushAll()
  {
    $this->cache = array();

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
