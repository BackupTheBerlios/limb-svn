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

require_once(LIMB_DIR . '/core/template/components/form/input_form_element.class.php');

class js_checkbox_component extends input_form_element
{
	function render_attributes()
	{
		unset($this->attributes['value']);
		parent :: render_attributes();
	}
	
	function render_js_checkbox()
	{ 
		$id = $this->get_attribute('id');
		$name = $this->get_attribute('name');
		
		if ($this->get_attribute('value'))
			$checked = 'checked=\'on\'';
		else	
			$checked = '';		

		$name = $this->_process_name_attribute($name);
		$js = "onclick=\"this.form.elements['{$name}'].value = 1*this.checked\"";
		
		echo "<input type='checkbox' id='{$id}_checkbox' {$checked} {$js}>";

	}

} 
?>