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
require_once(LIMB_DIR  . '/class/lib/db/DbTable.class.php');

class UserInGroupDbTable extends DbTable
{
  function _defineDbTableName()
  {
    return 'user_in_group';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'user_id' => array('type' => 'numeric'),
      'group_id' => array('type' => 'numeric'),
    );
  }
}

?>