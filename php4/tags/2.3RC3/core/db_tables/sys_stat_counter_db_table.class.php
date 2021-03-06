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
require_once(LIMB_DIR . '/core/lib/db/db_table.class.php');

class sys_stat_counter_db_table extends db_table
{
  function _define_columns()
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