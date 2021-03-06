<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: sys_tree_db_table.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');

class SysMaterializedPathTreeDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'sys_tree';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'parent_id' => array('type' => 'numeric'),
      'root_id' => array('type' => 'numeric'),
      'level' => array('type' => 'numeric'),
      'identifier' => '',
      'path' => '',
    );
  }
}

?>