<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: error_list.class.php 410 2004-02-06 10:46:51Z server $
*
***********************************************************************************/ 
/**
* Container for errors implementing the Iterator iterface
* 
* @todo documention - check that err object is validation_error
*/
class error_list
{
	/**
	* Indexed array of validation_error objects
	* 
	* @var array 
	* @access private 
	*/
	var $errors = array();
	
 	function & instance()
  {
  	$class_name = 'error_list';
  	$obj =& $GLOBALS['global_' . $class_name];

  	if(get_class($obj) != $class_name)
  	{
  		include_once(LIMB_DIR . 'core/lib/validators/error_list.class.php');
  		
  		$obj = & new $class_name();
  		$GLOBALS['global_' . $class_name] =& $obj;
  	}
  	
  	return $obj;
  }
  
  function error_list()
  {
  }

	/**
	* Add a validation_error object
	* 
	* @return void 
	* @access public 
	*/
	function add_error($field_name, $error_msg, $params=array())
	{
		$this->errors[$field_name][] = array('error' => $error_msg, 'params' => $params);
	} 
	
	function get_errors($field_name)
	{
		if(isset($this->errors[$field_name]))
			return $this->errors[$field_name];
	}
	
	function reset()
	{
		$this->errors = array();
	}
	
	function export()
	{
		return $this->errors;
	}

} 

?>