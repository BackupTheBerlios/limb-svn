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

class FileObjectDbTable extends LimbDbTable
{
  function _defineColumns()
  {
    return array(
        'description' => '',
        'media_id' => ''
        'id' => array('type' => 'int')
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