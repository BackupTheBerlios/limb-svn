<?php

require_once(LIMB_DIR . 'core/template/components/form/form_element.class.php');

class input_checkbox_component extends form_element
{
	/**
	* Overrides then calls with the parent render_attributes() method dealing
	* with the special case of the checked attribute
	* 
	* @return void 
	* @access protected 
	*/
	function render_attributes()
	{
		$value = $this->get_value();
		
		if ($value)
			$this->attributes['checked'] = 1;
		else
			unset($this->attributes['checked']);
		
		parent :: render_attributes();
	} 
} 
?>