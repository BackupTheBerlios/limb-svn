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

class ImageVariationDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'image_variation';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'image_id' => array('type' => 'numeric'),
      'media_id' => array('type' => 'numeric'),
      'width' => array('type' => 'numeric'),
      'height' => array('type' => 'numeric'),
      'variation' => '',
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