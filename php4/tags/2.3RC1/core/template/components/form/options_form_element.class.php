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
require_once(LIMB_DIR . '/core/template/components/form/container_form_element.class.php');
require_once(LIMB_DIR . '/core/template/components/form/option_renderer.class.php');

class options_form_element extends container_form_element
{
	var $default_value;
	
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
		$this->_set_options();
		
		if (empty($this->option_renderer))
		{
			$this->option_renderer = new option_renderer();
		} 		
		
		$this->_render_options();
	} 
	
	function set_default_value($value)
	{
		$this->default_value = $value;
	}

	function get_default_value()
	{
		return $this->default_value;
	}

	function get_value()
	{
		$value = parent :: get_value();
			
		if(!$default_value = $this->get_default_value())
			$default_value = reset($this->choice_list);
			
		if (!array_key_exists($value, $this->choice_list))
			return $default_value;
		else
			return $value;	
	}	
	
	function _set_options()
	{
		if($this->_use_ini_options())
		{
			$this->_set_options_from_ini_file();
		}
		elseif($this->_use_strings_options())
		{
			$this->_set_options_from_strings_file();
		}
		elseif ($this->_use_datasource_options())
		{
			$this->_set_options_from_datasource();
		}
	}
		
	function _use_ini_options()
	{
		return $this->get_attribute('options_ini_file') && $this->get_attribute('use_ini');
	}
	
	function _use_strings_options()
	{
		return $this->get_attribute('options_ini_file') && !$this->get_attribute('use_ini');
	}
	
	function _use_datasource_options()
	{
		return $this->get_attribute('options_datasource');
	}
	
	function _render_options()
	{
		$value = $this->get_value();
				
		foreach($this->choice_list as $key => $contents)
		{
			$this->option_renderer->render_attribute($key, $contents, $key == $value);
		} 
	}
	
	function _set_options_from_ini_file()
	{
		$ini_file = $this->get_attribute('options_ini_file');
		
		$this->set_choices(get_ini_option($ini_file . '.ini', 'options', 'constants'));
		if (!$this->get_default_value())
			$this->set_default_value(get_ini_option($ini_file . '.ini', 'default_option', 'constants'));
	}

	function _set_options_from_strings_file()
	{
		if($locale_type = $this->get_attribute('locale_type'))
		{
			if(strtolower($locale_type) == 'content')
				$locale_constant = 'CONTENT_LOCALE_ID';	
			else
				$locale_constant = 'MANAGEMENT_LOCALE_ID';	
		}
		else
			$locale_constant = 'MANAGEMENT_LOCALE_ID';	
	
		$ini_file = $this->get_attribute('options_ini_file');
		
		$this->set_choices(strings :: get('options', $ini_file, constant($locale_constant)));
		
		$this->set_default_value(strings :: get('default_option', $ini_file, constant($locale_constant)));
	}
	
	function _set_options_from_datasource()
	{
		$datasource = $this->_get_datasource();
		
		$this->set_choices($datasource->get_options_array());
		
		$this->set_default_value($datasource->get_default_option());
	}
	
	function & _get_datasource()
	{
    include_once(LIMB_DIR . '/core/datasource/datasource_factory.class.php');
    
		$datasource_path = $this->get_attribute('options_datasource');		
    
		return datasource_factory :: create($datasource_path);
	}
} 

?>