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
require_once(LIMB_DIR . '/class/lib/db/db_table.class.php');

class media_db_table extends db_table
{
  protected function _define_columns()
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