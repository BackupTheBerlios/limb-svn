<?php

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