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
require_once(LIMB_DIR . '/class/lib/db/db_table.class.php');

class sys_stat_day_counters_db_table extends db_table
{
  protected function _define_columns()
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