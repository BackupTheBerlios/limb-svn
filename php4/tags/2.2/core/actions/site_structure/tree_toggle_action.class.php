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

class tree_toggle_action extends action
{
	function perform(&$request, &$response)
	{
		if($request->has_attribute('recursive_search_for_node'))
			return;
		
		$tree =& tree :: instance();
				
		if(!$id = $request->get_attribute('id'))
			$id = get_mapped_id();
			
		if($request->has_attribute('expand'))
			$result = $tree->expand_node($id);
		elseif($request->has_attribute('collapse'))
			$result = $tree->collapse_node($id);
		else
			$result = $tree->toggle_node($id);
			
		if(!$result)
		  $request->set_status(REQUEST_STATUS_FAILURE);
	}
}

?>