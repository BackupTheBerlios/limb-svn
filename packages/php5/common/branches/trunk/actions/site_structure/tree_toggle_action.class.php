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
require_once(LIMB_DIR . 'class/core/actions/action.class.php');

class tree_toggle_action extends action
{
	public function perform($request, $response)
	{
		if($request->has_attribute('recursive_search_for_node'))
			return;
		
		$tree = tree :: instance();
		$tree->initialize_expanded_parents();
				
		if(!$id = $request->get('id'))
			$id = get_mapped_id();
			
		if($request->has_attribute('expand'))
			$result = $tree->expand_node($id);
		elseif($request->has_attribute('collapse'))
			$result = $tree->collapse_node($id);
		else
			$result = $tree->toggle_node($id);
			
		if(!$result)
		  $request->set_status(request :: STATUS_FAILURE);
	}
}

?>