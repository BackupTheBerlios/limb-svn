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

class phpbb_posts_db_table extends db_table
{
	var $_primary_key_name = 'post_id';
	
  function _define_columns()
  {
  	return array(
			'post_id' => array('type' => 'numeric'),
			'topic_id' => array('type' => 'numeric'),
			'forum_id' => array('type' => 'numeric'),
			'poster_id' => array('type' => 'numeric'),
			'post_time' => array('type' => 'numeric'),
			'poster_ip' => '',
			'post_username' => '',
			'enable_bbcode' => array('type' => 'numeric'),
			'enable_html' => array('type' => 'numeric'),
			'enable_smilies' => array('type' => 'numeric'),
			'enable_sig' => array('type' => 'numeric'),
			'post_edit_time' => array('type' => 'numeric'),
			'post_edit_count' => array('type' => 'numeric'),
    );
  }

  function _define_constraints()
  {
  	return array(
    	'post_id' =>	array(
    		array(
					'table_name' => 'phpbb_posts_text',
					'field' => 'post_id',
				)
			)
		);
	}  
}

?>