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
require_once(LIMB_DIR . 'class/tree/tree.class.php');
require_once(LIMB_DIR . 'class/validators/rules/single_field_rule.class.php');

define('TREE_IDENTIFIER_RULE_UNKNOWN_NODE_ID', -1000);

class tree_identifier_rule extends single_field_rule
{
	var $parent_node_id;
	var $node_id;
	
	function tree_identifier_rule($field_name, $parent_node_id, $node_id = TREE_IDENTIFIER_RULE_UNKNOWN_NODE_ID)
	{
		$this->node_id = $node_id;
		$this->parent_node_id = $parent_node_id;
		
		parent :: single_field_rule($field_name);
	} 

	function validate(&$dataspace)
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
						
			if($this->node_id == TREE_IDENTIFIER_RULE_UNKNOWN_NODE_ID)
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