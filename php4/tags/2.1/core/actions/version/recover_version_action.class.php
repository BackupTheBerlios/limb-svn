<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: set_group_access.class.php 38 2004-03-13 14:25:46Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/actions/action.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_no_reload_response.class.php');

class recover_version_action extends action
{
	function recover_version_action()
	{		
		parent :: action();
	}
	
	function perform()
	{
		if(!isset($_REQUEST['version']))
			return new close_popup_no_reload_response(RESPONSE_STATUS_FAILURE);

		if(!isset($_REQUEST['version_node_id']))
			return new close_popup_no_reload_response(RESPONSE_STATUS_FAILURE);
			
		$version = (int)$_REQUEST['version'];
		$node_id = (int)$_REQUEST['version_node_id'];
		
		if(!$site_object = wrap_with_site_object(fetch_one_by_node_id($node_id)))
			return new close_popup_no_reload_response(RESPONSE_STATUS_FAILURE);
		
		if(!is_subclass_of($site_object, 'content_object'))
			return new close_popup_no_reload_response(RESPONSE_STATUS_FAILURE);

		if($site_object->recover_version($version))
			return new close_popup_response(RESPONSE_STATUS_SUCCESS);
		else
			return new close_popup_no_reload_response(RESPONSE_STATUS_FAILURE);
	}

}

?>