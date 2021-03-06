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
require_once(LIMB_DIR . '/core/lib/validators/error_list.class.php');

class validator
{
	/**
	* Indexed array of rule objects
	* 
	* @see rule
	* @var array 
	* @access private 
	*/
	var $rules = array();
	
	/**
	* Whether the validation process was valid
	* 
	* @var boolean 
	* @access private 
	*/
	var $is_valid = true;

	function validator()
	{
	} 

	/**
	* Registers a rule
	* 
	* @param rule $ 
	* @return void 
	* @access public 
	*/
	function add_rule(&$rule)
	{
		$rule->set_error_list($this->_get_error_list());
		$this->rules[] =& $rule;
	} 
	
	function & _get_error_list()
	{
		return error_list :: instance();
	}

	/**
	* Whether the validation process was valid
	* 
	* @return boolean TRUE if valid
	* @access public 
	*/
	function is_valid()
	{
		return $this->is_valid;
	} 

	/**
	* Perform the validation
	* 
	* @return void 
	* @access public 
	*/
	function validate(&$dataspace)
	{		
		foreach($this->rules as $key => $rule)
		{
			$this->rules[$key]->validate($dataspace);
			$this->is_valid = (bool)($this->is_valid & $this->rules[$key]->is_valid());
		}
		return $this->is_valid;
	} 
} 

?>