<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/db_tables/content_object_db_table.class.php');

class photogallery_object_db_table extends content_object_db_table
{
  function _define_columns()
  {
    return array(
      'image_id' => array('type' => 'numeric'),
      'annotation' => '',
    );
  }
}

?>