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
require_once(LIMB_DIR . '/class/core/tree/tree.class.php');
require_once(LIMB_DIR . '/class/validators/rules/single_field_rule.class.php');

class tree_path_rule extends single_field_rule
{	
	public function validate($dataspace)
	{
		$value = $dataspace->get($this->field_name);
		
		if(!Limb :: toolkit()->getTree()->get_node_by_path($value))
			$this->error(strings :: get('error_invalid_tree_path', 'error'));
	} 
} 

?>