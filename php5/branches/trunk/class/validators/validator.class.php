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
require_once(LIMB_DIR . '/class/validators/error_list.class.php');

class validator
{
	protected $rules = array();
	
	protected $is_valid = true;

	public function add_rule($rule)
	{
		$this->rules[] = $rule;
	} 
	
	protected function _get_error_list()
	{
		return error_list :: instance();
	}

	public function is_valid()
	{
		return $this->is_valid;
	} 

	public function validate($dataspace)
	{		
		foreach($this->rules as $key => $rule)
		{
      resolve_handle($this->rules[$key]);
		  
  		$this->rules[$key]->set_error_list($this->_get_error_list());
			$this->rules[$key]->validate($dataspace);
			$this->is_valid = (bool)($this->is_valid & $this->rules[$key]->is_valid());
		}
		return $this->is_valid;
	} 
	
	public function get_rules()
	{
		return $this->rules;
	}
	
	public function add_error($field_name, $error, $params=array())
	{
	  $error_list = $this->_get_error_list();
	  $error_list->add_error($field_name, $error, $params);
	}
} 

?>