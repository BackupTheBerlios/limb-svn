<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: sys_site_object_tree_db_table.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/lib/db/db_table.class.php');

class sys_materialized_path_tree_db_table extends db_table
{
  function _define_columns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'parent_id' => array('type' => 'numeric'),
      'root_id' => array('type' => 'numeric'),
      'object_id' => array('type' => 'numeric'),
      'level' => array('type' => 'numeric'),
      'identifier' => '',
      'path' => '',
    );
  }

  function _define_constraints()
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