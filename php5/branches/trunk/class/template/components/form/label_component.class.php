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
require_once(LIMB_DIR . 'class/template/tag_component.class.php');

class label_component extends tag_component
{
	/**
	* CSS class attribute to display on error
	*/
	protected $error_class;
	/**
	* CSS style attribute to display on error
	*/
	protected $error_style;

	/**
	* If either are set, assigns the attributes for error class or style
	*/
	public function set_error()
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