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
require_once(LIMB_DIR . '/class/template/components/form/form_element.class.php');

class input_radio_component extends form_element
{
	/**
	* Overrides then calls with the parent render_attributes() method dealing
	* with the special case of the checked attribute
	*/
	public function render_attributes()
	{
		$value = $this->get_value();
		
		if (isset($this->attributes['value']) && $value == $this->attributes['value'])
		{
			$this->attributes['checked'] = 1;
		} 
		else
		{
		  unset($this->attributes['checked']);
		} 
		parent::render_attributes();
	} 
} 
?>