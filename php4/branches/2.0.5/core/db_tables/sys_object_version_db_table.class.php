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
require_once(LIMB_DIR . '/core/lib/db/db_table.class.php');

class sys_object_version_db_table extends db_table
{
  function sys_object_version_db_table()
  {
    parent :: db_table();
  }

  function _define_columns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'object_id' => array('type' => 'numeric'),
      'version' => array('type' => 'numeric'),
      'creator_id' => array('type' => 'numeric'),
      'created_date' => array('type' => 'numeric'),
      'modified_date' => array('type' => 'numeric'),
    );
  }
}

?>