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

class CachePersister //abstract class
{
  var $id;

  function CachePersister($id = 'cache')
  {
    $this->id = $id;
  }

  function getId()
  {
    return $this->id;
  }

  function put($key, &$value, $group = 'default'){}
  function assign(&$variable, $key, $group = 'default'){}
  function flushValue($key, $group = 'default'){}
  function flushGroup($group){}
  function flushAll(){}
}
?>
