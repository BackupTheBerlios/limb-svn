<?php

require_once(LIMB_DIR . 'core/template/components/form/container_form_element.class.php');

class select_multiple_component extends container_form_element
{
	/**
	* A associative array of choices to build the option list with
	* 
	* @var array 
	* @access private 
	*/
	var $choice_list = array();
	/**
	* The object responsible for rendering the option tags
	* 
	* @var object 
	* @access private 
	*/
	var $option_renderer;

	/**
	* Override form_element method to deal with variable names containing
	* HTML array syntax.
	* 
	* @return array the contents of the value
	* @access private 
	*/
	function get_value()
	{
		$form_component = &$this->find_parent_by_class('form_component');
		$name = $this->attributes['name'];
		return $form_component->get(str_replace('[]', '', $name));
	} 

	/**
	* Sets the choice list. Passed an associative array, the keys become the
	* contents of the option value attributes and the values in the array
	* become the text contents of the option tag
	* 
	* @param array $ 
	* @return void 
	* @access protected 
	*/
	function set_choices($choice_list)
	{
		$this->choice_list = $choice_list;
	} 

	/**
	* Sets a list of values to be displayed as selected
	* 
	* @param array $ indexed array of selected values
	* @return void 
	* @access public 
	*/
	function set_selection($selection)
	{
		$form_component = &$this->find_parent_by_class('form_component');
		$form_component->set($this->attributes['name'], $selection);
	} 

	/**
	* Sets object responsible for rendering the attributes
	* 
	* @param object $ e.g. option_renderer
	* @return void 
	* @access protected 
	*/
	function set_attribute_renderer($option_renderer)
	{
		$this->option_renderer = $option_renderer;
	} 

	/**
	* Renders the contents of the the select tag, option tags being built by
	* the option handler. Called from with a compiled template render function.
	* 
	* @return void 
	* @access protected 
	*/
	function render_contents()
	{
		$values = $this->get_value();

		if (!is_array($values))
			$values = array(reset($this->choice_list));
		else
		{
			$found = false;
			foreach ($values as $value)
			{
				if (array_key_exists($value, $this->choice_list))
				{
					$found = true;
					break;
				} 
			} 
			if (!$found)
				$values = array(reset($this->choice_list));
		} 

		if (empty($this->option_renderer))
		{
			$this->option_renderer = new option_renderer();
		} 

		foreach($this->choice_list as $key => $contents)
		{
			$this->option_renderer->render_attribute($key, $contents, in_array($key, $values));
		} 
	} 
} 
?>