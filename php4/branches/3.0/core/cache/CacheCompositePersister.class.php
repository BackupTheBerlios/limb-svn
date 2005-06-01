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
require_once(LIMB_DIR . '/core/cache/CachePersister.class.php');

class CacheCompositePersister extends CachePersister
{
  var $persisters = array();

  function registerPersister(&$persister)
  {
    $this->persisters[] =& $persister;
  }

  function put($key, &$value, $group = 'default')
  {
    foreach(array_keys($this->persisters) as $index)
    {
      $this->persisters[$index]->put($key, $value, $group);
    }
  }

  function assign(&$variable, $key, $group = 'default')
  {
    foreach(array_keys($this->persisters) as $index)
    {
      if($this->persisters[$index]->assign($variable, $key, $group) === true)
      {
        $this->_putValueToPersisters($index, $variable, $key, $group);
        return true;
      }
    }

    return false;
  }

  function flushValue($key, $group = 'default')
  {
    foreach(array_keys($this->persisters) as $index)
    {
      $this->persisters[$index]->flushValue($key, $group);
    }
  }

  function flushGroup($group)
  {
    foreach(array_keys($this->persisters) as $index)
    {
      $this->persisters[$index]->flushGroup($group);
    }
  }

  function flushAll()
  {
    foreach(array_keys($this->persisters) as $index)
    {
      $this->persisters[$index]->flushAll();
    }
  }

  function _putValueToPersisters($index, &$value, $key, $group)
  {
    for($i = 0; $i < $index; $i++)
      $this->persisters[$i]->put($key, $value, $group);
  }
}
?>
