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

class SysObjectVersionDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'sys_object_version';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'object_id' => array('type' => 'numeric'),
      'version' => array('type' => 'numeric'),
      'creator_id' => array('type' => 'numeric'),
      'created_date' => array('type' => 'numeric'),
      'modified_date' => array('type' => 'numeric'),
    );
  }
}

?>