<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'class/core/actions/action_interface.interface.php');

class action implements action_interface
{
	protected $name = '';
	
	protected $dataspace = null;
	
	protected $view = null;
		
	function __construct()
	{
		$this->name = $this->_define_dataspace_name();
		
    include_once(LIMB_DIR . 'class/core/dataspace_registry.class.php');		
		$this->dataspace =& dataspace_registry :: get($this->name);
	}
	
	protected function _define_dataspace_name()
	{
	  return '';
	}
	
	public function set_view($view)
	{
		$this->view = $view;
	}
		
	public function perform($request, $response)
	{
	  $request->set_status(request :: STATUS_SUCCESS);
	}	
} 
?>