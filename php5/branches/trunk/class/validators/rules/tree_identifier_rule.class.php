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
require_once(LIMB_DIR . 'class/core/tree/tree.class.php');
require_once(LIMB_DIR . 'class/validators/rules/single_field_rule.class.php');

class tree_identifier_rule extends single_field_rule
{
  const UNKNOWN_NODE_ID = -1000;
  
	private $parent_node_id;
	private $node_id;
	
	function __construct($field_name, $parent_node_id, $node_id = self :: UNKNOWN_NODE_ID)
	{
		$this->node_id = $node_id;
		$this->parent_node_id = $parent_node_id;
		
		parent :: __construct($field_name);
	} 

	public function validate($dataspace)
	{
		if(!$value = $dataspace->get($this->field_name))
			return;
		
		$tree = tree :: instance();
		
		if(!$tree->is_node($this->parent_node_id))
			return;
				
		if(!$nodes = $tree->get_children($this->parent_node_id))
			return;
			
		foreach($nodes as $id => $node)
		{
			if($node['identifier'] != $value)
				continue;
						
			if($this->node_id == self :: UNKNOWN_NODE_ID)
			{				
				$this->error(strings :: get('error_duplicate_tree_identifier', 'error'));
				break;
			}
			elseif($id != $this->node_id)
			{
				$this->error(strings :: get('error_duplicate_tree_identifier', 'error'));
				break;
			}
		}
	} 
} 

?>