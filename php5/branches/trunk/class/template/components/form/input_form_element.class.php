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
require_once(LIMB_DIR . 'class/template/components/form/form_element.class.php');

abstract class input_form_element extends form_element
{
	/**
	* Overrides then calls with the parent render_attributes() method. Makes
	* sure there is always a value attribute, even if it's empty.
	* Called from within a compiled template render function.
	*/
	public function render_attributes()
	{
		$value = $this->get_value();

		if (!is_null($value))
		{
			$this->attributes['value'] = $value;
		}
		 
		parent :: render_attributes();
	} 
} 
?>