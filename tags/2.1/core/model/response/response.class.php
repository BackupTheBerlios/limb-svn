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
define('RESPONSE_STATUS_DONT_TRACK', 0);

define('RESPONSE_STATUS_SUCCESS_MASK', 15);
define('RESPONSE_STATUS_SUCCESS', 1);
define('RESPONSE_STATUS_FORM_SUBMITTED', 2);
define('RESPONSE_STATUS_FORM_DISPLAYED', 4);

define('RESPONSE_STATUS_PROBLEM_MASK', 240);
define('RESPONSE_STATUS_FORM_NOT_VALID', 16);
define('RESPONSE_STATUS_FAILURE', 32);

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
		return ($this->status & RESPONSE_STATUS_SUCCESS_MASK);
	}

	function is_problem()
	{
		return ($this->status & RESPONSE_STATUS_PROBLEM_MASK);
	}
	
} 
?>