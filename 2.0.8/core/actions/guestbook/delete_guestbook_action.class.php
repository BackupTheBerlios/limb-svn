<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: delete_guestbook_message_action.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/form_delete_site_object_action.class.php');

class delete_guestbook_action extends form_delete_site_object_action
{
	function delete_guestbook_action($name='delete_form')
	{		
		parent :: form_delete_site_object_action($name, array('site_object' => 'guestbook'));
	}
}

?>