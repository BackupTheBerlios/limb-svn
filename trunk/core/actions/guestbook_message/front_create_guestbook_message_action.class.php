<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: front_create_guestbook_message_action.class.php 564 2004-02-25 16:49:41Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/guestbook_message/create_guestbook_message_action.class.php');

class front_create_guestbook_message_action extends create_guestbook_message_action
{
	function front_create_guestbook_message_action()
	{
		parent :: create_guestbook_message_action('display');
	}

	function _valid_perform()
	{
		if (parent :: _valid_perform())
			reload();
	}		
}

?>