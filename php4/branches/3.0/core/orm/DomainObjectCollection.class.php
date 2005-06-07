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

class DomainObjectCollection extends IteratorDecorator
{
  var $collection = array();

  function DomainObjectCollection(&$collection)
  {
    $this->collection =& $collection;
  }

  function rewind()
  {
    $this->iterator = new ArrayDataSet($this->collection);
    parent :: rewind();
  }

  function & current()
  {
    $record = parent :: current();
    return $record->export();
  }

  function add(&$obj)
  {
    $this->collection[] =& $obj;
  }
}

?>
