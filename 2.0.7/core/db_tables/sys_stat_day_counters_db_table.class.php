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
require_once(LIMB_DIR . 'core/lib/db/db_table.class.php');

class sys_stat_day_counters_db_table extends db_table
{
  function sys_stat_day_counters_db_table()
  {
    parent :: db_table();
  }

  function _define_columns()
  {
  	return array(
  		'id' => array('type' => 'numeric'),
  		'time' => array('type' => 'numeric'),
  		'hosts' => array('type' => 'numeric'),
  		'hits' => array('type' => 'numeric'),
  		'home_hits' => array('type' => 'numeric'),
  		'audience' => array('type' => 'numeric'),
    );
  }
}

?>