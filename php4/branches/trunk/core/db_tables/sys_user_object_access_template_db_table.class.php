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

class sys_user_object_access_template_db_table extends db_table
{  
  function _define_columns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'controller_id' => array('type' => 'numeric'),
      'action_name' => '',
    );
  }
  
  function _define_constraints()
  {
  	return array(
    	'id' =>	array(
    		0 => array(
					'table_name' => 'sys_user_object_access_template_item',
					'field' => 'template_id'
				),
			),
		);
  }
}

?>