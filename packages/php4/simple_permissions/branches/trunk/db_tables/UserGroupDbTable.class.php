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
require_once(LIMB_DIR . '/class/db_tables/OneTableObjectDbTable.class.php');

class UserGroupDbTable extends OneTableObjectDbTable
{
  protected function _defineConstraints()
  {
    return array(
      'object_id' =>	array(
        0 => array(
          'table_name' => 'user_in_group',
          'field' => 'group_id'
        ),
        1 => array(
          'table_name' => 'sys_object_access',
          'field' => 'accessor_id'
        ),
        2 => array(
          'table_name' => 'sys_action_access',
          'field' => 'accessor_id'
        ),
        3 => array(
          'table_name' => 'sys_action_access_template_item',
          'field' => 'accessor_id'
        ),
      ),
    );
  }
}

?>