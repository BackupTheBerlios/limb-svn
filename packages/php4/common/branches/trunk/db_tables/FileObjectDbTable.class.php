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
require_once(LIMB_DIR . '/core/db_tables/OneTableObjectDbTable.class.php');

class FileObjectDbTable extends OneTableObjectDbTable
{
  function _defineColumns()
  {
    return ComplexArray :: array_merge(
      parent :: _defineColumns(),
      array(
        'description' => '',
        'media_id' => ''
      )
    );
  }

  function _defineConstraints()
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