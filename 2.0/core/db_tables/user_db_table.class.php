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
require_once(LIMB_DIR . 'core/db_tables/content_object_db_table.class.php');

class user_db_table extends content_object_db_table
{
  function user_db_table()
  {
    parent :: content_object_db_table();
  }
  
  function _define_columns()
  {
  	return array(
      'name' => '',
      'lastname' => '',
      'email' => '',
      'password' => '',
      'generated_password' => '',
    );
  }
  
  function _define_constraints()
  {
  	return array(
    	'id' =>	array(
    		0 => array(
					'table_name' => 'user_in_group',
					'field' => 'user_id',
				),
	  		1 => array(
					'table_name' => 'sys_object_access',
					'field' => 'accessor_id',
				),
	  		2 => array(
					'table_name' => 'sys_action_access',
					'field' => 'accessor_id',
				),
	  		3 => array(
					'table_name' => 'sys_user_object_access_template_item',
					'field' => 'user_id',
				),
			),
		);
  }
}

?>