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
	function _process_name_attribute($value)
	{		
		return parent :: _process_name_attribute($value) . '[]';
	}
	
	function _render_options()
	{
		$values = $this->get_value();
		
		if(!is_array($values))
		  $values = array();
				
		foreach($this->choice_list as $key => $contents)
		{
			$this->option_renderer->render_attribute($key, $contents, in_array($key, $values));
		} 
	}	
	
	function get_value()
	{
	  return container_form_element :: get_value();
	}
} 
?>