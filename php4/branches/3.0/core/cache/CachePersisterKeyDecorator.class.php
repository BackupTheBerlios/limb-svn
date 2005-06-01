<?php
/**********************************************************************************
* copyright 2004 BIT, _ltd. http://limb-project.com, mailto: support@limb-project.com
*
* released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: CacheRegistry.class.php 1341 2005-05-31 15:16:55Z pachanga $
*
***********************************************************************************/

class CachePersisterKeyDecorator
{
  var $persister = null;

  function CachePersisterKeyDecorator($persister)
  {
    $this->persister =& $persister;
  }

  function getId()
  {
    return $this->persister->getId();
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
