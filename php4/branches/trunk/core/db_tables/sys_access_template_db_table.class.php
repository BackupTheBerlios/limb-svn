<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/db/db_table.class.php');

class sys_access_template_db_table extends db_table
{
  function _define_columns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'controller_id' => array('type' => 'numeric'),
      'accessor_type' => array('type' => 'numeric'),
      'action_name' => '',
    );
  }

  function _define_constraints()
  {
    return array(
      'id' => array(
        0 => array(
          'table_name' => 'sys_access_template_item',
          'field' => 'template_id'
        ),
      ),
    );
  }
}

?>