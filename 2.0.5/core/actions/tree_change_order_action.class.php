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

class tree_change_order_action extends action
{
	function tree_change_order_action($name='')
	{
		parent :: action($name);
	}
	
	function perform()
	{
		$tree =& limb_tree :: instance();
		
		if(isset($_REQUEST['id']))
			$node_id = (int)$_REQUEST['id'];
		else
			$node_id = get_mapped_id();

		if (!isset($_REQUEST['direction']))
			return false;
		
		if (!$object_data = fetch_one_by_node_id($node_id))
		{
	    debug :: write_error('Node is not accessible',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array(
    			'node_id' => $node_id
    		)
    	);
			close_popup();
		}	
		
		$direction = $_REQUEST['direction'];
		if (!($direction == 'up' || $direction == 'down'))
		{
			debug :: write_error('Direction is not correct',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array(
    			'direction' => $direction
    		)
    	);
			close_popup();
		}	
				
		$tree->change_node_order($node_id, $direction);
		close_popup();
	}
}

?>