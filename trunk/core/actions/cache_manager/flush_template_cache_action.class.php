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
require_once(LIMB_DIR . 'core/lib/system/fs.class.php');

class flush_template_cache_action extends action
{
	function perform(&$request, &$response)
	{
	  $files = fs :: find_subitems(VAR_DIR . '/compiled', 'f');
	  foreach($files as $file)
	    unlink($file);
	  
		if($request->has_attribute('popup'))
		  $response->write(close_popup_response($request));
	  
	  $request->set_status(REQUEST_STATUS_SUCCESS);
	}
}

?>