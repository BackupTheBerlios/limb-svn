<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/db_tables/content_object_db_table.class.php');

class user_group_db_table extends content_object_db_table
{    
  protected function _define_constraints()
  {
  	return array(
	  	'object_id' =>	array(
	  		0 => array(
					'table_name' => 'user_in_group',
					'field' => 'group_id'
				),
	  		1 => array(
					'table_name' => 'sys_object_access',
					'field' => 'accessor_id'
				),
	  		2 => array(
					'table_name' => 'sys_action_access',
					'field' => 'accessor_id'
				),
	  		3 => array(
					'table_name' => 'sys_group_object_access_template_item',
					'field' => 'group_id'
				),
			),
		);
  }
}

?>