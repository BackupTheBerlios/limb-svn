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

class phpbb_posts_text_db_table extends db_table
{
	var $_primary_key_name = 'post_id';
	
  function _define_columns()
  {
  	return array(
      'post_id' => array('type' => 'numeric'),
			'bbcode_uid' => '',
			'post_subject' => '',
			'post_text' => '',
    );
  }
}

?>