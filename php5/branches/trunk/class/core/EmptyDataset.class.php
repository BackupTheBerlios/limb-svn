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
  public function reset(){}

  public function next()
  {
    return false;
  }

  public function get($name)
  {
    return '';
  }

  public function set($name, $value){}

  public function append($name, $value){}

  public function clear($name){}

  public function import($valuelist){}

  public function importAppend($valuelist){}

  public function export()
  {
    return array();
  }

  public function getTotalRowCount()
  {
    return 0;
  }

  public function counter()
  {
    return 0;
  }
}

?>