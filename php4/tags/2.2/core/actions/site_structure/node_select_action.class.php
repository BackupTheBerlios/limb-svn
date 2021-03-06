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

class node_select_action extends action
{
	function perform(&$request, &$response)
	{
	  $request->set_status(REQUEST_STATUS_DONT_TRACK);
	  
	  if(!$path = $request->get_attribute('path'))
	    return;
	 
	 if(!$node = map_url_to_node($path))
	    return;
	 
	  if(!$object = fetch_one_by_node_id($node['id']))
	    return;
	    
	  $dataspace =& $this->view->find_child('parent_node_data');
	  
	  $dataspace->import($object);
	}
}

?>