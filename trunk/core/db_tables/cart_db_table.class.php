<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR  . '/core/lib/db/db_table.class.php');

class cart_db_table extends db_table
{
  function _define_columns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'user_id' => array('type' => 'numeric'),
      'cart_id' => '',
      'cart_items' => array('type' => 'clob'),
      'last_activity_time' => array('type' => 'numeric')
    );
  }
}

?>