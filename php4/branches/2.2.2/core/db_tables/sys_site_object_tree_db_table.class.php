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

class sys_site_object_tree_db_table extends db_table
{
  function _define_columns()
  {
  	return array(
      'id' => array('type' => 'numeric'),
      'parent_id' => array('type' => 'numeric'),
      'root_id' => array('type' => 'numeric'),
      'object_id' => array('type' => 'numeric'),
			'path' => '',
      'level' => array('type' => 'numeric'),
      'identifier' => '',
      'priority' => array('type' => 'numeric'),
      'children' => array('type' => 'numeric'),
    );
  }
  
  function _define_constraints()
  {
  	return array(
    	'object_id' =>	array(
    		0 => array(
					'table_name' => 'sys_site_object',
					'field' => 'id',
				),
			),
		);
  }
}

?>