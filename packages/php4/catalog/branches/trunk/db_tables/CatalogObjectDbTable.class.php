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

class CatalogObjectDbTable extends OneTableObjectDbTable
{
  function _defineColumns()
  {
    return array(
      'image_id' => array('type' => 'numeric'),
      'annotation' => '',
      'content' => '',
    );
  }
}

?>