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

class faq_object_db_table extends one_table_object_db_table
{  
  protected function _define_columns()
  {
  	return complex_array :: array_merge(
			parent :: _define_columns(),
			array(
	      'question' => '',
	      'question_author' => '',
	      'question_author_email' => '',
	      'answer' => '',
	      'answer_author' => '',
	      'answer_author_email' => ''
	    )  
    );
  }
}

?>