<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

class empty_dataset
{
  function reset()
  {
  }

  function next()
  {
    return false;
  }

  function &get($name)
  {
    return '';
  }

  function set($name, $value)
  {
  }

  function append($name, $value)
  {
  }

  function clear($name)
  {
  }

  function import($valuelist)
  {
  }

  function import_append($valuelist)
  {
  }

  function &export()
  {
    return array();
  }

  function register_filter(&$filter)
  {
  }

  function prepare()
  {
  }

  function get_total_row_count()
  {
    return 0;
  }

  function counter()
  {
    return 0;
  }
}

?>