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

class catalog_object_db_table extends one_table_object_db_table
{
  protected function _define_columns()
  {
    return array(
      'image_id' => array('type' => 'numeric'),
      'annotation' => '',
      'content' => '',
    );
  }
}

?>