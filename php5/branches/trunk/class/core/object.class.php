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
	protected $_attributes = null;
	
	function __construct()
	{
    $this->_attributes = new dataspace();
	}

	public function merge($attributes)
	{		
	  $this->_attributes->merge($attributes);
	}
	
	public function import($attributes)
	{
	  $this->_attributes->import($attributes);
	}
		
	public function export()
	{
		return $this->_attributes->export();
	}
	
	public function has_attribute($name)
	{
	  return $this->_attributes->get($name) !== null;
	}
		
	public function get($name, $default_value=null)
	{
		return $this->_attributes->get($name, $default_value);
	}
	
	public function set($name, $value)
	{
		$this->_attributes->set($name, $value);
	}

	public function destroy($name)
	{
		$this->_attributes->destroy($name);
	}

	public function reset()
	{
		$this->_attributes->reset();
	}
	
}

?>