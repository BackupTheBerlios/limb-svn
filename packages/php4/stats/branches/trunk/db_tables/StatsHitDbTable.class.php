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

class StatsHitDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'stats_hit';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'stats_referer_id' => array('type' => 'numeric'),
      'stats_uri_id' => array('type' => 'numeric'),
      'time' => array('type' => 'numeric'),
      'ip' => '',
      'action' => '',
      'session_id' => '',
    );
  }
}

?>