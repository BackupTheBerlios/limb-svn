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
require_once(LIMB_DIR . '/core/actions/action.class.php');

class image_select_action extends action
{
	function perform(&$request, &$response)
	{
	  $request->set_status(REQUEST_STATUS_DONT_TRACK);
		$object =& fetch_requested_object();
		
	  session :: set('limb_image_select_working_path', $object['path']);

	}
}
?>