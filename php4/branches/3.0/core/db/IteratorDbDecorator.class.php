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
require_once(WACT_ROOT . 'iterator/' .
            (version_compare(phpversion(), '5', '>=') ? 'iterator5.inc.php': 'iterator4.inc.php'));

class IteratorDbDecorator extends IteratorDecorator
{
  function paginate(&$pager)
  {
    $this->iterator->paginate($pager);
  }

  function freeQuery()
  {
    $this->iterator->freeQuery();
  }

  function getRowCount()
  {
    return $this->iterator->getRowCount();
  }

  function getTotalRowCount()
  {
    return $this->iterator->getTotalRowCount();
  }
}

?>
