<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: informer_object_db_table.class.php 59 2004-03-22 13:54:41Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/db_tables/content_object_db_table.class.php');

class informer_object_db_table extends content_object_db_table
{
  function informer_object_db_table()
  {
    parent :: content_object_db_table();
  }
  
  function _define_columns()
  {
  	return array(
      'code' => '',
    );
  }
}

?>