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
require_once(LIMB_DIR . 'class/template/components/form/input_checkbox_component.class.php');

class grid_checkbox_component extends input_checkbox_component
{
	public function get_value()
	{
		return $this->find_parent_by_class('list_component')->get_by_index_string($this->_make_index_name($this->attributes['name']));
	}
	
	public function set_value($value)
	{
	}
	
	protected function _process_name_attribute($value)
	{
		$list =& $this->find_parent_by_class('list_component');

		return 'grid_form' . $this->_make_index_name($value) . '[' . $list->get('node_id') . ']';
	}

} 

?>