<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: sys_stat_counter_db_table.class.php 59 2004-03-22 13:54:41Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/lib/db/db_table.class.php');

class sys_stat_search_phrase_db_table extends db_table
{
  function sys_stat_search_phrase_db_table()
  {
    parent :: db_table();
  }

  function _define_columns()
  {
  	return array(
  		'id' => array('type' => 'numeric'),
  		'phrase' => '',
  		'engine' => '',
  		'time' => array('type' => 'numeric'),
    );
  }
}

?>