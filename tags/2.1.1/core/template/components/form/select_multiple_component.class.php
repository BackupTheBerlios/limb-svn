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

require_once(LIMB_DIR . 'core/template/components/form/options_form_element.class.php');

class select_multiple_component extends options_form_element
{

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
	* Renders the contents of the the select tag, option tags being built by
	* the option handler. Called from with a compiled template render function.
	* 
	* @return void 
	* @access protected 
	*/
	function render_contents()
	{
		parent :: render_contents();
		
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