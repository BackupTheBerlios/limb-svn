<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/core/actions/action_interface.interface.php');

class empty_action implements action_interface
{
	public function set_view($view)
	{
	}
		
	public function perform($request, $response)
	{
	  $request->set_status(request :: STATUS_SUCCESS);
	}
} 


?>