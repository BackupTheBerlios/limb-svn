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
require_once(LIMB_DIR . 'core/model/response/response.class.php');

class exit_response extends response
{
	function exit_response($status=RESPONSE_STATUS_SUCCESS)
	{
		parent :: response($status);
	}
	
	function perform()
	{
		exit;
	}
} 
?>