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
define('RESPONSE_STATUS_FAILURE', 0);
define('RESPONSE_STATUS_SUCCESS', 1);
define('RESPONSE_STATUS_NOT_VALID', 2);

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
	
} 
?>