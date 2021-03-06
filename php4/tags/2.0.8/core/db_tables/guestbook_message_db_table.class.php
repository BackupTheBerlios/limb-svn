<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: guestbook_message_db_table.class.php 470 2004-02-18 13:04:56Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/db_tables/content_object_db_table.class.php');

class guestbook_message_db_table extends content_object_db_table
{
  function guestbook_message_db_table()
  {    
    parent :: content_object_db_table();
  }
  
  function _define_columns()
  {
  	return array(
      'message' => '',
      'sender_email' => '',
      'sender' => '',
      'comment' => '',
      'comment_author' => '',
      'comment_author_email' => '',
    );
  }
}

?>