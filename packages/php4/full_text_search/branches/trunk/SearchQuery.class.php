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

class SearchQuery
{
  protected $items = array();

  public function add($item)
  {
    $this->items[] = $item;
  }

  public function toString()
  {
    return implode(' ', $this->items);
  }

  public function getQueryItems()
  {
    return $this->items;
  }

  public function isEmpty()
  {
    return (sizeof($this->items) == 0);
  }
}

?>