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

class image_object_db_table extends content_object_db_table
{  
  protected function _define_columns()
  {
  	return complex_array :: array_merge(
  		parent :: _define_columns(),
  		array(
      	'description' => ''
      )	
    );
  }
  
  protected function _define_constraints()
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