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

class period_news_object_db_table extends content_object_db_table
{
  function _define_columns()
  {
    return array(
      'content' => '',
      'news_date' => array('type' => 'date'),
      'start_date' => array('type' => 'date'),
      'finish_date' => array('type' => 'date'),
      'annotation' => '',
    );
  }
}

?>