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
require_once(LIMB_DIR . '/class/lib/db/LimbDbTable.class.php');

class SysStatLogDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'sys_stat_log';
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
      'user_id' => array('type' => 'numeric'),
      'status' => array('type' => 'numeric'),
    );
  }
}

?>