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

class file_object_db_table extends one_table_object_db_table
{
  protected function _define_columns()
  {
    return complex_array :: array_merge(
      parent :: _define_columns(),
      array(
        'description' => '',
        'media_id' => ''
      )
    );
  }

  protected function _define_constraints()
  {
    return array(
      'media_id' =>	array(
          0 => array(
            'table_name' => 'media',
            'field' => 'id',
          ),
      ),
    );
  }
}

?>