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

class tree_move_item_action extends action
{
	function perform()
	{
		if(($mode = $this->_get_mode_from_request()) === false)
			return new failed_response();
				
		if(!isset($_REQUEST['id']))
			return new failed_response();
			
		$node_id = (int)$_REQUEST['id'];

		if(!isset($_REQUEST['target_id']))
			return new failed_response();

		$target_node_id = (int)$_REQUEST['target_id'];

		if(!$this->_check_nesting_rules($node_id, $target_node_id, $mode))
			return new close_popup_response(RESPONSE_STATUS_FAILURE);

		$tree =& tree :: instance();
							
		if($tree->move_tree($node_id, $target_node_id, $mode))
			return new close_popup_response();
		else
			return new close_popup_response(RESPONSE_STATUS_FAILURE);
	}
	
	function _check_nesting_rules($node_id, $target_node_id, $mode)
	{
		$tree =& tree :: instance();
			
		if($mode == NESE_MOVE_AFTER || $mode == NESE_MOVE_BEFORE)
		{
			$target_node = $tree->get_node($target_node_id);
			
			return $this->_can_parent_accept_node($target_node['parent_id'], $node_id);
		}
		elseif($mode == NESE_MOVE_BELOW)
			return $this->_can_parent_accept_node($target_node_id, $node_id);
	}
	
	function _get_mode_from_request()
	{
		if (!isset($_REQUEST['mode']))
			return false;
			
		switch($_REQUEST['mode'])
		{
			case 'before':
				return NESE_MOVE_BEFORE;
			break;
			
			case 'after':
				return NESE_MOVE_AFTER;
			break;

			case 'below':
				return NESE_MOVE_BELOW;
			break;
			
			default:
				return false;
		}
	}
	
	function _can_parent_accept_node($parent_node_id, $node_id)
	{
		if(!$parent_object = wrap_with_site_object(fetch_one_by_node_id($parent_node_id)))
		{
	    debug :: write_error('acceptor node is not accessible',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array(
    			'parent_node_id' => $parent_node_id
    		)
    	);

			return false;
		}
			
		if(!$target_data = fetch_one_by_node_id($node_id))
		{
	    debug :: write_error('node is not accessible',
    		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
    		array(
    			'node_id' => $node_id
    		)
    	);

			return false;
		}
		
		return $parent_object->can_accept_child_class($target_data['class_name']);
	}
}

?>