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
require_once(LIMB_DIR . 'core/actions/action.class.php');
require_once(LIMB_DIR . 'core/validators/validator.class.php');

class form_action extends action
{
	var $validator = null;

	var $validated = false;

	var $valid = false;
	
	function form_action()
	{
		$this->validator =& new validator();
				
		parent :: action();
	}
				
	function is_first_time(&$request)
	{
		if($this->name)
		{
		  if($arr = $request->get_attribute($this->name))
		  {		  
			  return (isset($arr['submitted']) ? false : true);
			}
			return true;
		}
		else
		{
		  $res = $request->get_attribute('submitted');
			return empty($res);
		}
	} 

	function _init_validator()
	{
	} 

	function validate()
	{
		if (!$this->validated)
		{
			$this->_init_validator();			
			
			$this->valid = $this->validator->validate($this->dataspace);
			
			$this->validated = true;
		}
		return $this->valid;
	} 

	function is_valid()
	{
		return $this->valid;
	} 

	function perform(&$request, &$response)
	{
		if ($this->is_first_time($request))
		{
			$this->_init_dataspace($request);
			
			$this->_first_time_perform($request, $response);
		} 
		else
		{
			$this->_transfer_dataspace($request);

			$this->_process_transfered_dataspace();
			
			if(!$this->validate())
			  $request->set_status(REQUEST_STATUS_FORM_NOT_VALID);
			else	
				$this->_valid_perform($request, $response);
				
			if($this->view && $form =& $this->view->find_child($this->name))
				$form->set_valid_status($request->is_success());
		} 
	}
	
	function _htmlspecialchars_dataspace_value($name)
	{
		if($value = $this->dataspace->get($name))
		{
			$value = htmlspecialchars($value);
			$this->dataspace->set($name, $value);
		}
	}
	
	function _init_dataspace(&$request)
	{
	}
	
	function _transfer_dataspace(&$request)
	{	
		if ($arr = $request->get_attribute($this->name))
			$this->dataspace->import($arr);
		else
			$this->dataspace->import($request->export_attributes());
	}

	function _process_transfered_dataspace()
	{	
	}

	function _first_time_perform(&$request, &$response)
	{
		$request->set_status(REQUEST_STATUS_FORM_DISPLAYED);
	}
		
	function _valid_perform(&$request, &$response)
	{
		$request->set_status(REQUEST_STATUS_FORM_SUBMITTED);
	}
	
	function get_validator()
	{
		return $this->validator;
	}
} 


?>