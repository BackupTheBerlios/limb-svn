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
require_once(LIMB_DIR . 'core/actions/login_action.class.php');

class phpbb_login_action extends login_action
{
	function phpbb_login_action($name = 'login_form')
	{
		parent :: login_action($name);
	}
		
	function _login_redirect($redirect)
	{
		$redirect = add_url_query_items($redirect, array('sid' => session :: get('phpbb_sid')));
		reload($redirect);
	}

}

?>