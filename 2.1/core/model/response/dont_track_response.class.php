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

class dont_track_response extends response
{
	function dont_track_response()
	{
		parent :: response(RESPONSE_STATUS_DONT_TRACK);
	}
} 
?>