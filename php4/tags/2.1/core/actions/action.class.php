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
require_once(LIMB_DIR . 'core/model/response/failed_response.class.php');

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
		return new response();
	}	
} 
?>