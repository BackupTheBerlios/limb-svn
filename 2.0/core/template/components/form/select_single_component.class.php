<?php

require_once(LIMB_DIR . 'core/template/components/form/container_form_element.class.php');
require_once(LIMB_DIR . 'core/template/components/form/option_renderer.class.php');

class select_single_component extends container_form_element
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
	* Sets a single option to be displayed as selected
	* 
	* @param string $ value which is selected
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
		$value = $this->get_value();

		if($ini_file = $this->get_attribute('options_ini_file'))
		{
			$this->set_choices(strings :: get('options', $ini_file));
			
			if($value === null)
				$value = strings :: get('default_option', $ini_file);
		}
		elseif ($this->get_attribute('options_datasource'))
		{
			$data_source =& data_source_factory :: create($this->get_attribute('options_datasource'));
			
			$this->set_choices($data_source->get_options_array());
			
			if($value === null)
				$value = $data_source->get_default_option();
		}

		if (empty($value) || !array_key_exists($value, $this->choice_list))
		{
			$value = reset($this->choice_list);
		} 

		if (empty($this->option_renderer))
		{
			$this->option_renderer = new option_renderer();
		} 
		
		foreach($this->choice_list as $key => $contents)
		{
			$this->option_renderer->render_attribute($key, $contents, $key == $value);
		} 
	} 
} 

?>