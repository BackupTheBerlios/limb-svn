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
require_once(LIMB_DIR . '/class/lib/db/LimbDbTable.class.php');

class MediaDbTable extends LimbDbTable
{
  function _defineDbTableName()
  {
    return 'media';
  }

  function _defineColumns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'media_file_id' => '',
      'file_name' => '',
      'mime_type' => '',
      'size' => array('type' => 'numeric'),
      'etag' => '',
    );
  }
}

?>