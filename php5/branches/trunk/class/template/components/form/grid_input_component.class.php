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
require_once(LIMB_DIR . 'class/template/components/form/input_form_element.class.php');

class grid_input_component extends input_form_element
{
  protected $hash_id = 'node_id';
  
	public function get_value()
	{
		return $this->find_parent_by_class('list_component')->get($this->attributes['name']);
	}
	
	public function set_value($value)
	{
	}

	public function render_attributes()
	{ 
	  if (isset($this->attributes['hash_id']))
	    $this->hash_id = $this->attributes['hash_id'];
	    
	  unset($this->attributes['hash_id']);
	  
	  parent :: render_attributes();
	}
		
	protected function _process_name_attribute($value)
	{
		$list =& $this->find_parent_by_class('list_component');
    
		return 'grid_form' . $this->_make_index_name($value) . '[' . $list->get($this->hash_id) . ']';
	}
	
} 

?>