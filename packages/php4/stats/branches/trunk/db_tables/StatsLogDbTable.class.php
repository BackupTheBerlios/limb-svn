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

class StatsLogDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'stats_log';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'node_id' => array('type' => 'numeric'),
      'stat_referer_id' => array('type' => 'numeric'),
      'stat_uri_id' => array('type' => 'numeric'),
      'time' => array('type' => 'numeric'),
      'ip' => '',
      'action' => '',
      'session_id' => '',
    );
  }
}

?>