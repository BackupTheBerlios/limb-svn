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
require_once(LIMB_DIR . 'class/lib/db/db_table.class.php');

class sys_node_link_db_table extends db_table
{
  function _define_columns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'linker_node_id' => array('type' => 'numeric'),
      'target_node_id' => array('type' => 'numeric'),
      'group_id' => array('type' => 'numeric'),
      'priority' => array('type' => 'numeric'),
    );
  }
}

?>