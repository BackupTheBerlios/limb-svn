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

class tree_toggle_action extends action
{
	function tree_toggle_action($name='')
	{
		parent :: action($name);
	}
	
	function perform()
	{
		$tree =& limb_tree :: instance();
				
		if(isset($_REQUEST['id']))
			$id = (int)$_REQUEST['id'];
		else
			$id = get_mapped_id();
			
		if(isset($_REQUEST['expand']))
			return $tree->expand_node($id);
		elseif(isset($_REQUEST['collapse']))
			return $tree->collapse_node($id);
		else
			return $tree->toggle_node($id);
	}
}

?>