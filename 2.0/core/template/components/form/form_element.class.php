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


require_once(LIMB_DIR . 'core/template/tag_component.class.php');

/**
* Base class for concrete form elements
*/
class form_element extends tag_component
{
	/**
	* Whether the form element has validated successfully (default TRUE)
	* 
	* @var boolean 
	* @access private 
	*/
	var $is_valid = true;

	/**
	* Name of the form element (for the name attribute)
	* 
	* @var string 
	* @access protected 
	*/
	var $display_name;

	/**
	* CSS class attribute the element should display if there is an error
	* 
	* @var string 
	* @access private 
	*/
	var $error_class;

	/**
	* CSS style attribute the element should display if there is an error
	* 
	* @var string 
	* @access private 
	*/
	var $error_style;
	
	var $attach_form_prefix = true;

	/**
	* Returns a value for the name attribute. If $this->display_name is not
	* set, returns either the title, alt or name attribute (in that order
	* of preference, defined for the tag
	* 
	* @return string 
	* @access protected 
	*/
	function get_field_name()
	{
		if (isset($this->display_name))
		{
			return $this->display_name;
		} 
		elseif (isset($this->attributes["title"]))
		{
			return $this->attributes["title"];
		} 
		elseif (isset($this->attributes["alt"]))
		{
			return $this->attributes["alt"];
		} 
		else
		{
			return str_replace("_", " ", $this->attributes["name"]);
		} 
	} 

	/**
	* Returns true if the form element is in an error state
	* 
	* @return boolean 
	* @access protected 
	*/
	function is_valid()
	{
		return !$this->is_valid;
	} 

	/**
	* Puts the element into the error state and assigns the error class or
	* style attributes, if the corresponding member vars have a value
	* 
	* @return boolean 
	* @access protected 
	*/
	function set_error()
	{
		$this->is_valid = false;
		if (isset($this->error_class))
		{
			$this->attributes['class'] = $this->error_class;
		} 
		if (isset($this->error_style))
		{
			$this->attributes['style'] = $this->error_style;
		} 
	} 

	/**
	* Returns the value of the form element
	* (the contents of the value attribute)
	* 
	* @return string 
	* @access public 
	*/
	function get_value()
	{
		$form_component =& $this->find_parent_by_class('form_component');

		$dataspace = dataspace :: instance($form_component->attributes['name']);
		
		if(!isset($this->attributes['name']))
			debug :: write_warning("form element 'name' attribute not set:" . $this->get_server_id());
		
		return $dataspace->get_by_index_string($this->_make_index_name($this->attributes['name']));
	} 
	
	function render_errors()
	{
		$error_list =& error_list :: instance();
		
		if($errors = $error_list->get_errors($this->id))
		{
			echo '<script language="javascript">';
			
			foreach($errors as $error_data)
			{
				echo "set_error('{$this->id}', '" . addslashes($error_data['error']) . "');";
			}
			
			echo '</script>';
		}
	}
	
	function render_js_validation()
	{
		echo '';
	}
	
	function _make_index_name($name)
	{
		return preg_replace('/^([^\[\]]+)(\[.*\])*$/', "[\\1]\\2", $name);		
	}
	
	function render_attributes()
	{
		$form_component =& $this->find_parent_by_class('form_component');
		
		$form_name = $form_component->attributes['name'];
		
		foreach ($this->attributes as $name => $value)
		{
			if($this->attach_form_prefix && $name == 'name')
			{						
				$value = $form_name . $this->_make_index_name($value);
			}
			
			if (!is_null($value))
			{
				echo ' ';
				echo $name;

				echo '="';
				echo htmlspecialchars($value, ENT_QUOTES);
				echo '"';
			} 
		} 
	} 
	
} 
?>