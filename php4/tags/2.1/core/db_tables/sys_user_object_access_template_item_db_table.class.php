<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: sys_user_object_access_template_item_db_table.class.php 410 2004-02-06 10:46:51Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/db/db_table.class.php');

class sys_user_object_access_template_item_db_table extends db_table
{
  function sys_user_object_access_template_item_db_table()
  {
    parent :: db_table();
  }

  function _define_columns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'template_id' => array('type' => 'numeric'),
      'user_id' => array('type' => 'numeric'),
      'r' => array('type' => 'numeric'),
      'w' => array('type' => 'numeric'),
    );
  }
}

?>