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
require_once(LIMB_DIR . '/class/db_tables/one_table_object_db_table.class.php');

class poll_db_table extends one_table_object_db_table
{
  protected function _define_columns()
  {
  	return complex_array :: array_merge(
  		parent :: _define_columns(),
  		array(
	      'restriction' => array('type' => 'numeric'),
	      'start_date' => array('type' => 'date'),
	      'finish_date' => array('type' => 'date')
	    )  
    );
  }

  protected function _define_constraints()
  {
  	return array(
    	'id' =>	array(
    		0 => array(
					'table_name' => 'poll_ip',
					'field' => 'poll_id',
				),
			),
		);
  }
}

?>