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


require_once(LIMB_DIR . 'core/template/components/form/input_hidden_component.class.php');

class request_state_component extends input_hidden_component
{ 
	var $attach_form_prefix = false;
	
	function get_value()
	{
		return isset($_REQUEST[$this->attributes['name']]) ? $_REQUEST[$this->attributes['name']] : '';
	}
} 
?>