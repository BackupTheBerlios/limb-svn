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
require_once(LIMB_DIR . '/class/template/components/form/input_form_element.class.php');

class color_picker_component extends input_form_element
{
	public function init_color_picker()
	{
		if (defined('COLOR_PICKER_LOAD_SCRIPT'))
			return;
					
		echo "<script type='text/javascript' src='/shared/js/color_picker.js'></script>";
		
		$this->set_attribute('onChange', "relateColor(this.id, this.value)");
		if(!$this->get_attribute('size'))
			$this->set_attribute('size', "10");
			
		define('COLOR_PICKER_LOAD_SCRIPT',1);
	}
	
	public function render_color_picker()
	{ 
		$id = $this->get_attribute('id');
		
		echo "&nbsp;<a href=\"javascript:pickColor('{$id}');\" id=\"{$id}_picker\"
					style=\"border: 1px solid #000000; font-family:Verdana; font-size:10px; 
					text-decoration: none;\">&nbsp;&nbsp;&nbsp;</a>
					<script language=\"javascript\">relateColor('{$id}', getObj('{$id}').value);</script>";

	}
	
} 
?>