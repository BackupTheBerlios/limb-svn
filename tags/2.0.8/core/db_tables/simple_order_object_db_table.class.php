<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: school_user_db_table.class.php 22 2004-03-01 15:10:52Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/db_tables/content_object_db_table.class.php');

class simple_order_object_db_table extends content_object_db_table
{
  function simple_order_object_db_table()
  {
    parent :: content_object_db_table();
  }
  
  function _define_columns()
  {
  	return array(
      'time' => array('type' => 'numeric'),
      'user_id' => array('type' => 'numeric'),
      'content' => '',
    );
  }
}

?>