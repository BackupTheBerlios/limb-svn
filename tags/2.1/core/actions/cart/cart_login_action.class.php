<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: send_cart_order_action.class.php 59 2004-03-22 13:54:41Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/login_action.class.php');
require_once(LIMB_DIR . 'core/model/response/redirect_response.class.php');

class cart_login_action extends login_action
{
	function cart_login_action()
	{		
		parent :: login_action();
	}
	
	function _valid_perform()
	{
		$response = parent :: _valid_perform();
		if($response->is_success())
			return new redirect_response($response->get_status(), '/root/cart?action=checkout&popup=1');
		else
			return $response;
	}
}

?>