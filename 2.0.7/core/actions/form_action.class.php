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
require_once(LIMB_DIR . 'core/lib/http/http_request.inc.php');
require_once(LIMB_DIR . 'core/actions/action.class.php');
require_once(LIMB_DIR . 'core/lib/validators/validator.class.php');
require_once(LIMB_DIR . 'core/model/response/not_valid_response.class.php');

class form_action extends action
{
	var $validator = null;

	var $validated = false;

	var $valid = false;
	
	function form_action($name='')
	{
		$this->validator =& new validator();
				
		parent :: action($name);
	}
				
	function is_first_time()
	{
		if($this->name)
			return isset($_REQUEST[$this->name]['submitted']) ? false : true;
		else
			return isset($_REQUEST['submitted']) ? false : true;
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

	function perform()
	{
		if ($this->is_first_time())
		{
			$this->_init_dataspace();
			
			return $this->_first_time_perform();
		} 
		else
		{
			$this->_transfer_dataspace();

			$this->_process_transfered_dataspace();
			
			if(!$this->validate())
			{
				$result =& new not_valid_response();

			  debug :: write_error('validation failed', 
				  __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__);
			}
			else	
				$result = $this->_valid_perform();
				
			if($this->view && $form =& $this->view->find_child($this->name))
				$form->set_valid_status($result->is_success());
			
			return $result;
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
	
	function _init_dataspace()
	{
	}
	
	function _transfer_dataspace()
	{	
		if (isset($_REQUEST[$this->name]))
			$this->dataspace->import($_REQUEST[$this->name]);
		else
			$this->dataspace->import($_REQUEST);		
	}

	function _process_transfered_dataspace()
	{	
	}

	function _first_time_perform()
	{
		return new response(RESPONSE_STATUS_FORM_DISPLAYED);
	}
		
	function _valid_perform()
	{
		return new response(RESPONSE_STATUS_FORM_SUBMITTED);
	}
	
	function get_validator()
	{
		return $this->validator;
	}
} 
?>