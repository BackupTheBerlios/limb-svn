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

class image_object_db_table extends content_object_db_table
{
  function image_object_db_table()
  {    
    parent :: content_object_db_table();
  }
  
  function _define_columns()
  {
  	return array(
      'description' => '',
    );
  }
  
  function _define_constraints()
  {
  	return array(
    	'object_id' =>	array(
	    		0 => array(
						'table_name' => 'image_variation',
						'field' => 'image_id',
					),
			),
    );
  }
}

?>