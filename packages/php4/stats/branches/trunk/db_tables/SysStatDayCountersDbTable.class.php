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
require_once(LIMB_DIR . '/class/lib/db/DbTable.class.php');

class SysStatDayCountersDbTable extends DbTable
{
  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'time' => array('type' => 'numeric'),
      'hosts' => array('type' => 'numeric'),
      'hits' => array('type' => 'numeric'),
      'home_hits' => array('type' => 'numeric'),
      'audience' => array('type' => 'numeric'),
    );
  }
}

?>