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
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');

class SysStatCounterDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'sys_stat_counter';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'hosts_all' => array('type' => 'numeric'),
      'hits_all' => array('type' => 'numeric'),
      'hosts_today' => array('type' => 'numeric'),
      'hits_today' => array('type' => 'numeric'),
      'time' => array('type' => 'numeric'),
    );
  }
}

?>