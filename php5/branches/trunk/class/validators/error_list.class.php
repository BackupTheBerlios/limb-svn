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
* Container for errors implementing the Iterator iterface
* 
* @todo documention - check that err object is validation_error
*/
class error_list
{
  static protected $instance = null;
  
	protected $errors = array();
	
	static public function instance()
	{
    if (!self :: $instance)
      self :: $instance = new error_list();

    return self :: $instance;	
	}		
	  
	public function add_error($field_name, $error_msg, $params=array())
	{
		$this->errors[$field_name][] = array('error' => $error_msg, 'params' => $params);
	} 
	
	public function get_errors($field_name)
	{
		if(isset($this->errors[$field_name]))
			return $this->errors[$field_name];
	}
	
	public function reset()
	{
		$this->errors = array();
	}
	
	public function export()
	{
		return $this->errors;
	}
} 

?>