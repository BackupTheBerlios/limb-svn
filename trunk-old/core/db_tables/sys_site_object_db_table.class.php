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

class sys_site_object_db_table extends db_table
{
  function sys_site_object_db_table()
  {
    parent :: db_table();
  }

  function _define_columns()
  {
  	return array(
      'id' => array('type' => db_types::NUMERIC()),
      'class_id' => array('type' => db_types::NUMERIC()),
      'status' => array('type' => db_types::NUMERIC()),
      'title' => array('type' => db_types::VARCHAR()),
      'identifier' => array('type' => db_types::VARCHAR()),
      'current_version' => array('type' => db_types::VARCHAR()),
      'creator_id' => array('type' => db_types::VARCHAR()),
      'created_date' => array('type' => db_types::VARCHAR()),
      'modified_date' => array('type' => db_types::VARCHAR()),
      'locale_id' => array('type' => db_types::VARCHAR()),
    );
  }
  
  function _define_constraints()
  {
  	return array(
    	'id' =>	array(
    		array(
					'table_name' => 'sys_object_version',
					'field' => 'object_id',
				),
	  		array(
					'table_name' => 'sys_object_access',
					'field' => 'object_id'
				),
	  		array(
					'table_name' => 'sys_full_text_index',
					'field' => 'object_id'
				),
			)
		);
  }
}

?>