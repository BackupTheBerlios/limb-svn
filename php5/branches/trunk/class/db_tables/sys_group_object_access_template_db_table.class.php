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
require_once(LIMB_DIR . 'class/lib/db/db_table.class.php');

class sys_group_object_access_template_db_table extends db_table
{  
  protected function _define_columns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'class_id' => array('type' => 'numeric'),
      'action_name' => '',
    );
  }
  
  protected function _define_constraints()
  {
  	return array(
    	'id' =>	array(
    		0 => array(
					'table_name' => 'sys_group_object_access_template_item',
					'field' => 'template_id'
				),
			),
		);
  }
}

?>