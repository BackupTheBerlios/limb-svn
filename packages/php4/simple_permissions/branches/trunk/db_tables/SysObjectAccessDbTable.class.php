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

class SysObjectAccessDbTable extends DbTable
{
  function _defineDbTableName()
  {
    return 'sys_object_access';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'object_id' => array('type' => 'numeric'),
      'accessor_id' => array('type' => 'numeric'),
      'access' => array('type' => 'numeric'),
      'accessor_type' => array('type' => 'numeric'),
    );
  }
}

?>