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

define('TREE_ERROR_NODE_NOT_FOUND', 1);
define('TREE_ERROR_NODE_WRONG_PARAM', 2);
define('TREE_ERROR_RECURSION', 3);

class tree_driver
{
	var $_expanded_parents = array();
	
	/**
	* Used for _internal_ tree conversion
	* 
	* @var bool Turn off user param verification and id generation
	* @access private 
	*/
	var $_dumb_mode = false;
	
	function tree_driver()
	{
	}
		
  function set_dumb_mode($status=true)
  {
  	$prev_mode = $this->_dumb_mode;
  	$this->_dumb_mode = $status;
  	return $prev_mode;
  }
	
	function set_expanded_parents(& $expanded_parents)
	{
		$this->_expanded_parents =& $expanded_parents;
				
		$this->check_expanded_parents();
	}
	
  function check_expanded_parents()
  {
  	if(!is_array($this->_expanded_parents) || sizeof($this->_expanded_parents) == 0)
  	{
  		$this->reset_expanded_parents(); 		
  	}
  	elseif(sizeof($this->_expanded_parents) > 0)
  	{
  		$this->update_expanded_parents();
  	}
  }
  
  function update_expanded_parents()
  {
  	$nodes_ids = array_keys($this->_expanded_parents);
  	
  	$nodes =& $this->get_nodes_by_ids($nodes_ids);
  	
  	foreach($nodes as $id => $node)
  		$this->_set_expanded_parent_status($node, $this->is_node_expanded($id));
  }
	
  function toggle_node($id)
  {
  	if(($node = $this->get_node($id)) === false)  		
  		return false;
		
		$this->_set_expanded_parent_status($node, !$this->is_node_expanded($id));
		  	
  	return true;
  }
  
  function expand_node($id)
  {
  	if(($node = $this->get_node($id)) === false)
  		return false;
  	
  	$this->_set_expanded_parent_status($node, true);
  	  	
  	return true;
  }

  function collapse_node($id)
  {
  	if(($node = $this->get_node($id)) === false)
  		return false;
  		
		$this->_set_expanded_parent_status($node, false);
		    
  	return true;
  }
  
  function reset_expanded_parents()
  {
  	$this->_expanded_parents = array();
  }
  
  function _set_expanded_parent_status($node, $status)
  {
		error('abstract method',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
		array());
  }
  
  function get_node()
  {
		error('abstract method',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
		array());
  }
  
  function get_nodes_by_ids()
  {
		error('abstract method',
		 __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
		array());
  }
}

?>