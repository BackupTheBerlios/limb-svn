<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: subscribe_theme_db_table.class.php 239 2004-02-29 19:00:20Z server $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/db_tables/content_object_db_table.class.php');

class subscribe_theme_db_table extends content_object_db_table
{
  function _define_columns()
  {
    return array(
      'mail_template' => '',
    );
  }
}

?>