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

class EmptyDataset
{
  function reset(){}

  function next()
  {
    return false;
  }

  function get($name)
  {
    return '';
  }

  function set($name, $value){}

  function append($name, $value){}

  function clear($name){}

  function import($valuelist){}

  function importAppend($valuelist){}

  function export()
  {
    return array();
  }

  function getTotalRowCount()
  {
    return 0;
  }

  function counter()
  {
    return 0;
  }
}

?>