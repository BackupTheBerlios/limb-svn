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

class user_in_group_db_table extends db_table
{
	var $_db_table_name = 'user_in_group';
	
  function user_in_group_db_table()
  {
    parent :: db_table();
  } 
  
  function _define_columns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'user_id' => array('type' => 'numeric'),
      'group_id' => array('type' => 'numeric'),
    );
  }
}

?>