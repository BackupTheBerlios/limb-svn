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
require_once(LIMB_DIR . 'core/lib/http/control_flow.inc.php');

class close_popup_no_reload_response extends response
{	
	function close_popup_no_reload_response($status = RESPONSE_STATUS_SUCCESS)
	{		
		parent :: response($status);
	}
					
	function perform()
	{
		close_popup_no_parent_reload();
		exit;
	}
	
} 


?>