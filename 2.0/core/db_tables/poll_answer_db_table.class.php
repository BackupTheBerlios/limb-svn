<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: poll_answer_db_table.class.php 467 2004-02-18 10:16:31Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/db_tables/content_object_db_table.class.php');

class poll_answer_db_table extends content_object_db_table
{
  function poll_answer_db_table()
  {
    parent :: content_object_db_table();
  }

  function _define_columns()
  {
  	return array(
      'count' => array('type' => 'numeric'),
    );
  }
}

?>