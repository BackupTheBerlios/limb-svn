<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: tree_identifier_rule.class.php 411 2004-02-06 15:33:37Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/tree/limb_tree.class.php');
require_once(LIMB_DIR . 'core/lib/validators/rules/single_field_rule.class.php');

class tree_identifier_rule extends single_field_rule
{
	var $parent_id = -1;
	var $current_identifier = '';
	
	function tree_identifier_rule($field_name, $parent_id, $current_identifier='')
	{
		$this->parent_id = $parent_id;
		$this->current_identifier = $current_identifier;
		
		parent :: single_field_rule($field_name);
	} 

	function validate(&$dataspace)
	{
		if(!$value = $dataspace->get($this->field_name))
			return;

		if(	$this->current_identifier &&
				$this->current_identifier == $value)
			return;
		
		$tree = limb_tree :: instance();
		
		if(!$nodes = $tree->get_children($this->parent_id))
			return;
			
		foreach($nodes as $node)
		{			
			if($node['identifier'] == $value)
			{
				$this->error('DUPLICATE_TREE_IDENTIFIER');
				break;
			}			
		}
	} 
} 

?>