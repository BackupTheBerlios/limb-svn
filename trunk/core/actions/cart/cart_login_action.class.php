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

class cart_login_action extends login_action
{
	function _valid_perform(&$request, &$response)
	{
		parent :: _valid_perform(&$request, &$response);
		
		if($request->is_success())
			$response->redirect('/root/cart?action=checkout&popup=1');
	}
}

?>