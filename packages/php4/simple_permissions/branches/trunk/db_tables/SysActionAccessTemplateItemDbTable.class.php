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

class SysActionAccessTemplateItemDbTable extends DbTable
{
  function _defineDbTableName()
  {
    return 'sys_action_access_template_item';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'template_id' => array('type' => 'numeric'),
      'accessor_id' => array('type' => 'numeric'),
      'access' => array('type' => 'numeric'),
    );
  }
}

?>