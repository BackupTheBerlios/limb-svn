<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: sys_class_db_table.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/db/db_table.class.php');

class sys_stat_ip_db_table extends db_table
{
  function sys_stat_ip_db_table()
  {
    parent :: db_table();
  }

  function _define_columns()
  {
  	return array(
  		'id' => '',
  		'time' => array('type' => 'numeric')
    );
  }
}

?>