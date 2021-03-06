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
require_once(LIMB_DIR . '/class/lib/db/DbTable.class.php');

class MediaDbTable extends DbTable
{
  protected function _defineColumns()
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