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
require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');
require_once(LIMB_DIR . 'core/actions/action.class.php');
require_once(LIMB_DIR . 'core/model/response/redirect_response.class.php');

class activate_password_action extends action
{
	function perform()
	{
		$object =& site_object_factory :: create('user_object');
		if(!$object->activate_password())
		{
			message_box :: write_notice('Password activation failed!');
			return new redirect_response(RESPONSE_STATUS_FAILED, '/');
		}
		
		return new response();
	}
}

?>