<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: phpbb_users_db_table.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/db/db_table.class.php');

class phpbb_sessions_db_table extends db_table
{
	var $_primary_key_name = 'session_id';
	
  function phpbb_sessions_db_table()
  {
    parent :: db_table();
  }

  function _define_columns()
  {
  	return array(
			'session_id' => '',
			'session_user_id' => array('type' => 'numeric'),
			'session_start' => array('type' => 'numeric'),
			'session_time' => array('type' => 'numeric'),
			'session_ip' => '',
			'session_page' => array('type' => 'numeric'),
			'session_logged_in' => array('type' => 'numeric'),
    );
  }
}

?>