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

class SysNodeLinkGroupDbTable extends DbTable
{
  protected function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'identifier' => '',
      'title' => '',
      'priority' => array('type' => 'numeric'),
    );
  }

  protected function _defineConstraints()
  {
    return array(
      'id' =>	array(
        array(
          'table_name' => 'sys_node_link',
          'field' => 'group_id',
        ),
      )
    );
  }

}

?>