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
/**
* Base class for defining rules to validate against
*/
class rule
{
	var $error_list = null;
	
	/**
	* Is this field valid?
	* 
	* @var boolean 
	* @access private 
	*/
	var $is_valid = true;
	
	function rule()
	{
	}
	
	function is_valid()
	{
		return $this->is_valid;
	} 
	
	function set_error_list(&$error_list)
	{
		$this->error_list = &$error_list;
	}
	
	function error()
	{
		error('ABSTRACTMETHOD', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
			array('method' => __FUNCTION__ . '()', 'class' => __CLASS__));
	}
	
	/**
	* Perform validation
	* 
	* @param error_list $ 
	* @return boolean (always TRUE is base class)
	* @access protected 
	* @abstract 
	*/
	function validate(&$dataspace, &$error_list)
	{
		error('ABSTRACTMETHOD', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
			array('method' => __FUNCTION__ . '()', 'class' => __CLASS__));
	} 
} 

?>