<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: subscribe_member_db_table.class.php 239 2004-02-29 19:00:20Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/db/db_table.class.php');

class subscribe_member_db_table extends db_table
{
  function subscribe_member_db_table()
  {
    parent :: db_table();
  }
  
  function _define_columns()
  {
  	return array(
			'id' => array('type' => 'numeric'),
      'member_id' => array('type' => 'numeric'),
      'theme_id' => array('type' => 'numeric'),
    );
  }
}

?>