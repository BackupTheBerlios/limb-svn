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
require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');
require_once(LIMB_DIR . 'core/actions/action.class.php');
require_once(LIMB_DIR . 'core/model/response/close_popup_response.class.php');

class node_select_action extends action
{
	function perform()
	{
	  if(!isset($_REQUEST['path']))
	    return new response(RESPONSE_STATUS_DONT_TRACK);
	 
	 if(!$node = map_url_to_node($_REQUEST['path']))
	    return new response(RESPONSE_STATUS_DONT_TRACK);
	 
	  if(!$object = fetch_one_by_node_id($node['id']))
	    return new response(RESPONSE_STATUS_DONT_TRACK);
	    
	  $dataspace =& $this->view->find_child('parent_node_data');
	  
	  $dataspace->import($object);
	  							
		return new response(RESPONSE_STATUS_DONT_TRACK);
	}
}

?>