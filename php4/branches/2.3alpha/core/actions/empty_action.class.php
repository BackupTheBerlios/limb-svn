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
class empty_action
{
	function set_view(&$view)
	{
	}
		
	function perform(&$request, &$response)
	{
	  $request->set_status(REQUEST_STATUS_SUCCESS);
	}
} 


?>