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

class close_popup_response extends response
{
	var $parent_reload_url = '';
	var $search_for_node = false;
	
	function close_popup_response($status = RESPONSE_STATUS_SUCCESS, $reload_url = RELOAD_SELF_URL, $search_for_node = false)
	{
		$this->parent_reload_url = $reload_url;
		$this->search_for_node = $search_for_node;
		
		parent :: response($status);
	}
					
	function perform()
	{
		close_popup($this->parent_reload_url, $this->search_for_node);
		exit;
	}
	
} 


?>