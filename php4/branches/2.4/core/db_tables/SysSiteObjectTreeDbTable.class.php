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

class SysSiteObjectTreeDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'sys_site_object_tree';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'parent_id' => array('type' => 'numeric'),
      'root_id' => array('type' => 'numeric'),
      'object_id' => array('type' => 'numeric'),
      'path' => '',
      'level' => array('type' => 'numeric'),
      'identifier' => '',
      'priority' => array('type' => 'numeric'),
      'children' => array('type' => 'numeric'),
    );
  }

  function _defineConstraints()
  {
    return array(
      'object_id' =>	array(
        0 => array(
          'table_name' => 'sys_site_object',
          'field' => 'id',
        ),
      ),
    );
  }
}

?>