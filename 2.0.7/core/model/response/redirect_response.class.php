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

class redirect_response extends response
{
	var $url = '';
	
	function redirect_response($status = RESPONSE_STATUS_SUCCESS, $url = PHP_SELF)
	{
		$this->url = $url;
		
		parent :: response($status);
	}
					
	function perform()
	{
		reload($this->url);
		exit;
	}
	
} 


?>