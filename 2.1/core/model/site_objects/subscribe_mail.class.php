<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: subscribe_mail.class.php 239 2004-02-29 19:00:20Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/content_object.class.php');
require_once(LIMB_DIR . 'core/lib/mail/send_html_mail.inc.php');

class subscribe_mail extends content_object
{
	function subscribe_mail()
	{
		parent :: content_object();
	}

	function _define_class_properties()
	{
		return array(
			'class_ordr' => 0,
			'can_be_parent' => 0,
			'controller_class_name' => 'subscribe_mail_controller',
			'auto_identifier' => true
		);
	}

	function create()
	{
		$mail_id = parent :: create();

		if ($mail_id === false)
			return false;

		$this->_send_mail();
		return $mail_id;
	}
	
	
	function _send_mail()
	{
		$title = $this->get_attribute('title');		
		$content = $this->get_attribute('content');
		$author = $this->get_attribute('author');
		
		$subscribe_email = ADMINISTRATOR_EMAIL; //fix
		
		$parent_object_data =& fetch_mapped_by_url();

		$recipients = $this->_get_theme_subscribers($parent_object_data['id']);
		
		if(!count($recipients))
			return false;

		foreach($recipients as $recipient)
		{
			@ send_html_mail(
						array('"' . $recipient['name'].'" <'. $recipient['email'] .'>'),
						'"' . $author.'" <'. $subscribe_email .'>', $title,
						$content);
		}
		
		return true;
	}

	
	function _get_theme_subscribers($theme_id)
	{
		$db_table	=  & db_table_factory :: instance('subscribe_member');
		$subscribers = $db_table->get_list(array('theme_id' => $theme_id), '', 'member_id');
		
		if (!count($subscribers))
			return array();
			
		$members_list = $this->_get_subscribers_from_branch('/root/members');
		$users_list = $this->_get_subscribers_from_branch('/root/users');
		
		$total_list = array_merge($members_list, $users_list);
		
		foreach($total_list as $key => $data)
		{
			$user_id = $data['id'];
			if(array_key_exists($user_id, $subscribers))
			{
				$subscribers[$user_id]['email'] = $data['email'];
				$subscribers[$user_id]['name'] = $data['name'];
			}
		}
		
		return $subscribers;
	}
	
	function _get_subscribers_from_branch($path)
	{
		$params = array(
			'restrict_by_class' => false
		);

		
		$temp_list =& fetch_sub_branch($path, 'site_object', $counter, $params);

		if (count($temp_list))
		{
			$record = reset($temp_list);
			$subscribers_class_name = $record['class_name'];
		}	
		else
			return array();
		
		$subscribers_list =& fetch_sub_branch($path, $subscribers_class_name, $counter);
		if (is_array($subscribers_list) && count($subscribers_list))
			return $subscribers_list;
		else
			return array();	
	}
}

?>