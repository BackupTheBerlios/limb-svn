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

class SysActionAccessTemplateDbTable extends DbTable
{
  protected function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'behaviour_id' => array('type' => 'numeric'),
      'accessor_type' => array('type' => 'numeric'),
      'action_name' => '',
    );
  }

  protected function _defineConstraints()
  {
    return array(
      'id' =>	array(
        0 => array(
          'table_name' => 'sys_action_access_template_item',
          'field' => 'template_id'
        ),
      ),
    );
  }
}

?>