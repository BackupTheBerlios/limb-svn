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
require_once(LIMB_DIR . '/core/lib/db/db_table.class.php');

class sys_access_template_item_db_table extends db_table
{
  function _define_columns()
  {
    return array(
      'id' => array('type' => 'numeric'),
      'template_id' => array('type' => 'numeric'),
      'accessor_id' => array('type' => 'numeric'),
      'access' => array('type' => 'numeric'),
    );
  }
}

?>