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

class ImageObjectDbTable extends OneTableObjectDbTable
{
  function _defineColumns()
  {
    return ComplexArray :: array_merge(
      parent :: _defineColumns(),
      array(
        'description' => ''
      )
    );
  }

  function _defineConstraints()
  {
    return array(
      'object_id' =>	array(
          0 => array(
            'table_name' => 'image_variation',
            'field' => 'image_id',
          ),
      ),
    );
  }
}

?>