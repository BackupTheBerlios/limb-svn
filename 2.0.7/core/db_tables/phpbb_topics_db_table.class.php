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

class phpbb_topics_db_table extends db_table
{
	var $_primary_key_name = 'topic_id';
	
  function phpbb_topics_db_table()
  {
    parent :: db_table();    
  }

  function _define_columns()
  {
  	return array(
      'topic_poster' => array('type' => 'numeric'),
			'topic_id' => array('type' => 'numeric'),
			'forum_id' => array('type' => 'numeric'),
			'topic_title' => '',
			'topic_time' => array('type' => 'numeric'),
			'topic_views' => array('type' => 'numeric'),
			'topic_replies' => array('type' => 'numeric'),
			'topic_status' => array('type' => 'numeric'),
			'topic_vote' => array('type' => 'numeric'),
			'topic_type' => array('type' => 'numeric'),
			'topic_first_post_id' => array('type' => 'numeric'),
			'topic_last_post_id' => array('type' => 'numeric'),
			'topic_moved_id' => array('type' => 'numeric'),
    );
  }
}

?>