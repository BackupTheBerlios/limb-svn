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

	function merge($attributes)
	{		
	  $this->_attributes->merge($attributes);
	}
	
	function import($attributes)
	{
	  $this->_attributes->import($attributes);
	}
		
	function export()
	{
		return $this->_attributes->export();
	}
	
	function has_attribute($name)
	{
	  return $this->_attributes->get($name) !== null;
	}
		
	function get($name, $default_value=null)
	{
		return $this->_attributes->get($name, $default_value);
	}
	
	function set($name, $value)
	{
		$this->_attributes->set($name, $value);
	}

	function destroy($name)
	{
		$this->_attributes->destroy($name);
	}

	function reset()
	{
		$this->_attributes->reset();
	}
	
}

?>