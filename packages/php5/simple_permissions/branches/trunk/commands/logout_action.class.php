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

class logout_action extends action
{
	public function perform($request, $response)
	{
		Limb :: toolkit()->getUser()->logout();
		
		$response->redirect('/');
	}
}

?>