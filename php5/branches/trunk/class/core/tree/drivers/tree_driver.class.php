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
require_once(LIMB_DIR . 'class/core/tree/tree_interface.interface.php');

abstract class tree_driver
{  
	protected $_expanded_parents = array();
	
	/**
	* Used for _internal_ tree conversion
	*/
	protected $_dumb_mode = false;
			
  public function set_dumb_mode($status=true)
  {
  	$prev_mode = $this->_dumb_mode;
  	$this->_dumb_mode = $status;
  	return $prev_mode;
  }
	
	public function set_expanded_parents(& $expanded_parents)
	{
		$this->_expanded_parents =& $expanded_parents;
				
		$this->check_expanded_parents();
	}
	
  public function check_expanded_parents()
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
  
  public function update_expanded_parents()
  {
  	$nodes_ids = array_keys($this->_expanded_parents);
  	
  	$nodes = $this->get_nodes_by_ids($nodes_ids);
  	
  	foreach($nodes as $id => $node)
  		$this->_set_expanded_parent_status($node, $this->is_node_expanded($id));
  }
	
  public function toggle_node($id)
  {
  	if(($node = $this->get_node($id)) === false)  		
  		return false;
		
		$this->_set_expanded_parent_status($node, !$this->is_node_expanded($id));
		  	
  	return true;
  }
  
  public function expand_node($id)
  {
  	if(($node = $this->get_node($id)) === false)
  		return false;
  	
  	$this->_set_expanded_parent_status($node, true);
  	  	
  	return true;
  }

  public function collapse_node($id)
  {
  	if(($node = $this->get_node($id)) === false)
  		return false;
  		
		$this->_set_expanded_parent_status($node, false);
		    
  	return true;
  }
  
  public function reset_expanded_parents()
  {
  	$this->_expanded_parents = array();
  }  
}

?>