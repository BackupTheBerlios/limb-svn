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
require_once(LIMB_DIR . 'core/actions/action.class.php');

class activate_password_action extends action
{
	function perform(&$request, &$response)
	{
		$object =& site_object_factory :: create('user_object');
		if(!$object->activate_password())
		{
			message_box :: write_notice('Password activation failed!');
			
			$request->set_status(REQUEST_STATUS_FAILED);
			$response->redirect('/');
		}
	}
}

?>