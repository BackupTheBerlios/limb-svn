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
require_once(LIMB_DIR . 'core/lib/util/dataspace.class.php');

class action
{
	var $name = '';
	
	var $dataspace = null;
	
	var $view = null;

	function action($name='')
	{
		$this->name = $name;
		
		$this->dataspace =& dataspace :: instance($name);
	}
	
	function set_view(&$view)
	{
		$this->view =& $view;
	}
		
	function perform()
	{
		return true;
	}
	
	function _set($name, $value)
	{
		$this->dataspace->set($name, $value);
	}

	function _get($name)
	{
		return $this->dataspace->get($name);
	}
	
	function _import($arr)
	{
		$this->dataspace->import($arr);
	}
	
	function _export()
	{
		return $this->dataspace->export();
	}
	
} 


?>