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
require_once(LIMB_DIR . '/class/db_tables/one_table_object_db_table.class.php');

class user_db_table extends one_table_object_db_table
{
  protected function _define_columns()
  {
    return complex_array :: array_merge(
      parent :: _define_columns(),
      array(
      'name' => '',
      'lastname' => '',
      'email' => '',
      'password' => '',
      'generated_password' => '',
      )
    );
  }

  protected function _define_constraints()
  {
    return array(
      'object_id' =>	array(
        0 => array(
          'table_name' => 'user_in_group',
          'field' => 'user_id',
        ),
        1 => array(
          'table_name' => 'sys_object_access',
          'field' => 'accessor_id',
        ),
        2 => array(
          'table_name' => 'sys_action_access',
          'field' => 'accessor_id',
        ),
        3 => array(
          'table_name' => 'sys_action_access_template_item',
          'field' => 'accessor_id',
        ),
      ),
    );
  }
}

?>