<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: navigation_item_db_table.class.php 410 2004-02-06 10:46:51Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/db_tables/content_object_db_table.class.php');

class navigation_item_db_table extends content_object_db_table
{
  function navigation_item_db_table()
  {
    parent :: content_object_db_table();
  }

  function _define_columns()
  {
  	return array(
      'url' => '',
    );
  }
}

?>