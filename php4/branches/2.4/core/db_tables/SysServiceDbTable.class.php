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

class SysServiceDbTable extends LimbDbTable
{
  function _definePrimaryKeyName()
  {
    return 'service_id';
  }

  function _defineDbTableName()
  {
    return 'sys_service';
  }

  function _defineColumns()
  {
    return array(
      'service_id' => array('type' => 'numeric'),
      'oid' => array('type' => 'numeric'),
      'behaviour_id' => array('type' => 'numeric'),
      'title' => '',
    );
  }
}

?>