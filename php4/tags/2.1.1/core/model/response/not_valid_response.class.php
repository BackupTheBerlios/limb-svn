<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: action.class.php 2 2004-02-29 19:06:22Z server $
*
***********************************************************************************/ 

require_once(LIMB_DIR . 'core/model/response/response.class.php');

class not_valid_response extends response
{
	function not_valid_response()
	{
		parent :: response(RESPONSE_STATUS_FORM_NOT_VALID);
	}
} 
?>