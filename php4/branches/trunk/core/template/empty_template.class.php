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
class empty_template
{
  function empty_template()
  {
  }

  function find_parent_by_class($class)
  {
    return null;
  }

  function find_child_by_class($class)
  {
    return null;
  }

  function find_child($server_id)
  {
    return null;
  }


  function &get_child($server_id)
  {
    return null;
  }

  function display()
  {
    debug :: write_error('template is null', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
  }
}

?>