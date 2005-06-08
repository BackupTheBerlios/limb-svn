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
require_once(WACT_ROOT . '/iterator/iterator.inc.php');

class ObjectCollection extends IteratorBase
{
  var $collection = array();

  function ObjectCollection(&$collection)
  {
    $this->collection =& $collection;
  }

  function rewind()
  {
    $this->current = reset($this->collection);
    $this->key = key($this->collection);
    $this->valid = is_object($this->current);
  }

  function next()
  {
    $this->current = next($this->collection);
    $this->key = key($this->collection);
    $this->valid = is_object($this->current);
  }

  function add(&$obj)
  {
    $this->collection[] =& $obj;
  }
}

?>
