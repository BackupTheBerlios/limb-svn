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

class SysActionAccessDbTable extends DbTable
{
  protected function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'behaviour_id' => array('type' => 'numeric'),
      'accessor_id' => array('type' => 'numeric'),
      'action_name' => '',
      'accessor_type' => array('type' => 'numeric'),
    );
  }
}

?>