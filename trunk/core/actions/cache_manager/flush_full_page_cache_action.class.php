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
require_once(LIMB_DIR . 'core/actions/action.class.php');
require_once(LIMB_DIR . 'core/cache/full_page_cache_manager.class.php');

class flush_full_page_cache_action extends action
{
	function perform(&$request, &$response)
	{
	  $manager = new full_page_cache_manager();
    $manager->flush();	  
	  
	  $request->set_status(REQUEST_STATUS_SUCCESS);

		if($request->has_attribute('popup'))
		  $response->write_response_string(close_popup_response($request));
	}
}

?>