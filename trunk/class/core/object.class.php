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
require_once(LIMB_DIR . 'class/core/dataspace.class.php');

class object
{
	var $_attributes = null;
	
	function object()
	{
    $this->_attributes =& new dataspace();	
	}

	function import_attributes($attributes, $merge=true)
	{
		if($merge)
			$this->_attributes->merge($attributes);
		else
			$this->_attributes->import($attributes);
	}
	
	function export_attributes()
	{
		return $this->_attributes->export();
	}
	
	function has_attribute($name)
	{
	  return $this->_attributes->get($name) !== null;
	}
		
	function get_attribute($name, $default_value=null)
	{
		return $this->_attributes->get($name, $default_value);
	}
	
	function set_attribute($name, $value)
	{
		$this->_attributes->set($name, $value);
	}

	function unset_attribute($name)
	{
		$this->_attributes->destroy($name);
	}

	function reset_attributes()
	{
		$this->_attributes->reset();
	}
	
}

?>