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
require_once(LIMB_DIR . '/core/db/LimbDbTable.class.php');

class ImageObjectDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'image_object';
  }

  function _defineColumns()
  {
    return array(
        'id' => array('type' => 'int'),
        'title' => '',
        'description' => ''
      );
  }

  function _defineConstraints()
  {
    return array(
      'id' =>	array(
          0 => array(
            'table_name' => 'image_variation',
            'field' => 'image_id',
          ),
      ),
    );
  }
}

?>