<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: image_select_component.class.php 46 2004-03-19 12:45:55Z server $
*
***********************************************************************************/

require_once(LIMB_DIR . 'core/template/components/form/input_form_element.class.php');

class js_checkbox_component extends input_form_element
{
	
	function render_js_checkbox()
	{ 
		$id = $this->get_attribute('id');
		$name = $this->get_attribute('name');
		
		if ($this->get_attribute('value'))
			$checked = 'checked=\'on\'';
		else	
			$checked = '';		
		
		$js = "onclick='document.forms[this.form.name][\"{$name}\"].value = 1*this.checked'";
		
		echo "<input type='checkbox' id='{$id}_checkbox' {$checked} {$js}>";

	}

} 
?>