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
require_once(LIMB_DIR . '/core/lib/locale/strings.class.php');
require_once(LIMB_DIR . '/core/lib/validators/rules/rule.class.php');

/**
* rules responsbile for validating a single field descend from this class.
*/
class single_field_rule extends rule
{
	/**
	* field name to validate
	* 
	* @var string 
	* @access private 
	*/
	var $field_name;
	

	/**
	* Constructs rule
	* 
	* @param string $ field_name to validate
	* @access public 
	*/
	function single_field_rule($field_name)
	{
		$this->field_name = $field_name;
	} 

	/**
	* Returns the field_name the rule applies to
	* 
	* @return string name of field
	* @access public 
	*/
	function get_field_name()
	{
		return $this->field_name;
	} 

	/**
	* Signal that an error has occurred.
	* 
	* @param string $ id of the error
	* @param optional $ data regarding the error
	* @access protected 
	*/
	function error($error, $params=array())
	{
		$this->is_valid = false;
		
		if($this->error_list)
			$this->error_list->add_error($this->field_name, $error, $params);
	} 

	/**
	* Perform validation
	* 
	* @param dataspace $ - Data to validate
	* @param error_list $ 
	* @return boolean (always TRUE is base class)
	* @access public 
	*/
	function validate(&$dataspace)
	{
		$this->is_valid = true;
		$value = $dataspace->get($this->field_name);
		if (isset($value) && $value !== '')
		{
			$this->check($value);
		} 
		return $this->is_valid;
	} 

	/**
	* check a Single Value to see if its valid
	* 
	* @param value $ - to check
	* @access protected 
	* @abstract 
	*/
	function check($value)
	{
		error('ABSTRACTMETHOD', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__,
			array('method' => __FUNCTION__ . '()', 'class' => __CLASS__));
	} 
} 

?>