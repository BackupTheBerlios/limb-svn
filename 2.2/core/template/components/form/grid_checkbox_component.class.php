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

require_once(LIMB_DIR . 'core/template/components/form/input_checkbox_component.class.php');

class grid_checkbox_component extends input_checkbox_component
{
	function get_value()
	{
		$list =& $this->find_parent_by_class('list_component');
		
		return $list->get_by_index_string($this->_make_index_name($this->attributes['name']));
	}
	
	function set_value($value)
	{
	}
	
	function _process_name_attribute($value)
	{
		$list =& $this->find_parent_by_class('list_component');

		return 'grid_form' . $this->_make_index_name($value) . '[' . $list->get('node_id') . ']';
	}

} 

?>