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

class SysSiteObjectTreeDbTable extends DbTable
{
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