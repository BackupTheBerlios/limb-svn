<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/class/core/actions/action.class.php');

class activate_password_action extends action
{
	public function perform($request, $response)
	{
		$object = Limb :: toolkit()->createSiteObject('user_object');
		if(!$object->activate_password())
		{
			message_box :: write_notice('Password activation failed!');
			
			$request->set_status(request :: STATUS_FAILED);
			$response->redirect('/');
		}
	}
}

?>