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

require_once(LIMB_DIR . 'core/template/components/form/input_form_element.class.php');

class grid_input_component extends input_form_element
{
	function get_value()
	{
		$list =& $this->find_parent_by_class('list_component');

		return $list->get($this->attributes['name']);
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