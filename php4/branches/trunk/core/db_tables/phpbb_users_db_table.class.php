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

class phpbb_users_db_table extends db_table
{
	var $_primary_key_name = 'user_id';
	
  function _define_columns()
  {
  	return array(
			'user_id' => array('type' => 'numeric'),
			'user_active' => array('type' => 'numeric'),
			'username' => '',
			'user_password' => '',
			'user_session_time' => array('type' => 'numeric'),
			'user_session_page' => array('type' => 'numeric'),
			'user_lastvisit' => array('type' => 'numeric'),
			'user_regdate' => array('type' => 'numeric'),
			'user_level' => array('type' => 'numeric'),
			'user_posts' => array('type' => 'numeric'),
			'user_timezone' => array('type' => 'numeric'),
			'user_style' => array('type' => 'numeric'),
			'user_lang' => '',
			'user_dateformat' => '',
			'user_new_privmsg' => array('type' => 'numeric'),
			'user_unread_privmsg' => array('type' => 'numeric'),
			'user_last_privmsg' => array('type' => 'numeric'),
			'user_emailtime' => array('type' => 'numeric'),
			'user_viewemail' => array('type' => 'numeric'),
			'user_attachsig' => array('type' => 'numeric'),
			'user_allowhtml' => array('type' => 'numeric'),
			'user_allowbbcode' => array('type' => 'numeric'),
			'user_allowsmile' => array('type' => 'numeric'),
			'user_allowavatar' => array('type' => 'numeric'),
			'user_allow_pm' => array('type' => 'numeric'),
			'user_allow_viewonline' => array('type' => 'numeric'),
			'user_notify' => array('type' => 'numeric'),
			'user_notify_pm' => array('type' => 'numeric'),
			'user_popup_pm' => array('type' => 'numeric'),
			'user_rank' => array('type' => 'numeric'),
			'user_avatar' => '',
			'user_avatar_type' => array('type' => 'numeric'),
			'user_email' => '',
			'user_icq' => '',
			'user_website' => '',
			'user_from' => '',
			'user_sig' => '',
			'user_sig_bbcode_uid' => '',
			'user_aim' => '',
			'user_yim' => '',
			'user_msnm' => '',
			'user_occ' => '',
			'user_interests' => '',
			'user_actkey' => '',
			'user_newpasswd' => ''
    );
  }

  function _define_constraints()
  {
  	return array(
    	'user_id' =>	array(
    		array(
					'table_name' => 'phpbb_posts',
					'field' => 'poster_id',
				),
    		array(
					'table_name' => 'phpbb_topics',
					'field' => 'topic_poster',
				)
			)
		);
	}  
  
}

?>