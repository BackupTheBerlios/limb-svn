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
  var $items = array();

  function add($item)
  {
    $this->items[] = $item;
  }

  function toString()
  {
    return implode(' ', $this->items);
  }

  function getQueryItems()
  {
    return $this->items;
  }

  function isEmpty()
  {
    return (sizeof($this->items) == 0);
  }
}

?>