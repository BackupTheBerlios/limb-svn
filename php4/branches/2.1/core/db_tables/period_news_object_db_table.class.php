<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: school_news_object_db_table.class.php 21 2004-02-29 18:59:25Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/db_tables/content_object_db_table.class.php');

class period_news_object_db_table extends content_object_db_table
{
  function period_news_object_db_table()
  {
    parent :: content_object_db_table();
  }
  
  function _define_columns()
  {
  	return array(
      'content' => '',
      'news_date' => array('type' => 'date'),
      'start_date' => array('type' => 'date'),
      'finish_date' => array('type' => 'date'),
      'annotation' => '',
    );
  }
}

?>