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
require_once(LIMB_DIR . 'class/core/tree/tree.class.php');
require_once(LIMB_DIR . 'class/validators/rules/single_field_rule.class.php');

class tree_node_id_rule extends single_field_rule
{	
	protected function check($value)
	{
		if(empty($value))
		  $this->error(strings :: get('error_invalid_tree_node_id', 'error'));
		elseif(!tree :: instance()->get_node((int)$value))
			$this->error(strings :: get('error_invalid_tree_node_id', 'error'));
	} 
}
?>