<?php

require_once(LIMB_DIR . 'core/template/tag_component.class.php');

class label_component extends tag_component
{
	/**
	* CSS class attribute to display on error
	* 
	* @var string 
	* @access private 
	*/
	var $error_class;
	/**
	* CSS style attribute to display on error
	* 
	* @var string 
	* @access private 
	*/
	var $error_style;

	/**
	* If either are set, assigns the attributes for error class or style
	* 
	* @return void 
	* @access protected 
	*/
	function set_error()
	{
		if (isset($this->error_class))
		{
			$this->attributes['class'] = $this->error_class;
		} 
		if (isset($this->error_style))
		{
			$this->attributes['style'] = $this->error_style;
		} 
	} 
} 

?>