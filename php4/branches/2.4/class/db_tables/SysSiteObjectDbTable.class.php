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

class SysSiteObjectDbTable extends DbTable
{
  function _defineDbTableName()
  {
    return 'sys_site_object';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'class_id' => array('type' => 'numeric'),
      'behaviour_id' => array('type' => 'numeric'),
      'status' => array('type' => 'numeric'),
      'title' => '',
      'identifier' => '',
      'current_version' => array('type' => 'numeric'),
      'creator_id' => array('type' => 'numeric'),
      'created_date' => array('type' => 'numeric'),
      'modified_date' => array('type' => 'numeric'),
      'locale_id' => '',
    );
  }

  function _defineConstraints()
  {
    return array(
      'id' =>	array(
        array(
          'table_name' => 'sys_object_version',
          'field' => 'object_id',
        ),
        array(
          'table_name' => 'sys_node_link',
          'field' => 'target_node_id'
        ),
        array(
          'table_name' => 'sys_node_link',
          'field' => 'linker_node_id'
        ),
      )
    );
  }
}

?>