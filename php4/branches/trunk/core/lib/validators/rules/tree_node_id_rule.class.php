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
require_once(LIMB_DIR . '/core/tree/tree.class.php');
require_once(LIMB_DIR . '/core/lib/validators/rules/single_field_rule.class.php');

class tree_node_id_rule extends single_field_rule
{	
	function check($value)
	{ 
		$tree =& tree :: instance();
		
		if(empty($value))
		  $this->error(strings :: get('error_invalid_tree_node_id', 'error'));
		elseif(!$tree->get_node((int)$value))
			$this->error(strings :: get('error_invalid_tree_node_id', 'error'));
	} 
}
?>