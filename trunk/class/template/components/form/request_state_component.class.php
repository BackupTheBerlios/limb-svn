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
	function get_value()
	{	
		$form =& $this->find_parent_by_class('form_component');
				
		if($form->is_first_time())
		{
		  $request = request :: instance();
		  
		  if($value = $request->get_attribute($this->attributes['name']))
		    return $value;
		  else
		    return '';
		}
		else
		  return parent :: get_value();
	}	
} 
?>