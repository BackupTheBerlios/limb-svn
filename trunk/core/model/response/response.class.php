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
define('RESPONSE_STATUS_SUCCESS', 15);
define('RESPONSE_STATUS_FORM_NOT_SUBMITTED', 2);

define('RESPONSE_STATUS_FAILURE', 240);
define('RESPONSE_STATUS_FORM_NOT_VALID', 16);

class response
{
	var $status;
	
	function response($status = RESPONSE_STATUS_SUCCESS)
	{
		$this->status = $status;
	}
	
	function get_status()
	{
		return $this->status;
	}
			
	function perform()
	{
	}
	
	function is_success()
	{
		return ($this->status & RESPONSE_STATUS_SUCCESS);
	}

	function is_failure()
	{
		return ($this->status & RESPONSE_STATUS_FAILURE);
	}
	
} 
?>