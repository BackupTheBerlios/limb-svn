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
require_once(LIMB_DIR . '/class/lib/db/db_table.class.php');

class sys_site_object_db_table extends db_table
{
  function _define_columns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'class_id' => array('type' => 'numeric'),
      'status' => array('type' => 'numeric'),
      'title' => '',
      'identifier' => '',
      'current_version' => array('type' => 'numeric'),
      'creator_id' => array('type' => 'numeric'),
      'created_date' => array('type' => 'numeric'),
      'modified_date' => array('type' => 'numeric'),
      'locale_id' => '',
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
	  		array(
					'table_name' => 'sys_node_link',
					'field' => 'target_node_id'
				),
	  		array(
					'table_name' => 'sys_node_link',
					'field' => 'linker_node_id'
				),
			)
		);
  }
}

?>