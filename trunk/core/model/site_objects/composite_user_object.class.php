<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: user_object.class.php 470 2004-02-18 13:04:56Z mike $
*
***********************************************************************************/ 
require_once(LIMB_DIR . 'core/model/site_objects/user_object.class.php');

class composite_user_object extends user_object
{
	var $_node_objects = array();
	
	function composite_user_object()
	{
		parent :: user_object();
		
		$this->_create_node_objects();
	}
	
	function import_attributes($attributes, $merge=true)
	{
		parent :: import_attributes($attributes, $merge);
		
		$this->_walk_node_objects('import_attributes', array('data' => $data, 'merge' => $merge));		
	}
	
	function _create_node_objects()
	{
		$definitions = $this->_define_node_objects();
				
		foreach($definitions as $class_name)
			$this->_node_objects[$class_name] =& instantiate_object($class_name, '/core/model/');
	}
	
	function _define_node_objects()
	{
		return array();
	}
	
	function _synchronize_attributes()
	{
		$data =& $this->export_attributes();
		
		$this->_walk_node_objects('import_attributes', array('data' => $data));	
	}
	
	function create()
	{
		if(($result = parent :: create()) === false)
			return $result;
				
		$this->_synchronize_attributes();

		if($this->_walk_node_objects('create') === false)
			return false;
				
		return $result;		
	}
	
	function delete()
	{
		if(($result = parent :: delete()) === false)
			return $result;
				
		if($this->_walk_node_objects('delete') === false)
			return false;

		return $result;		
	}
	
	function update($force_create_new_version = true)		
	{
		if(($result = parent :: update($force_create_new_version)) === false)
			return $result;
		
		$this->_synchronize_attributes();
		
		if($this->_walk_node_objects('update') === false)
			return false;

		return $result;		
	}
	
	function change_password()
	{
		$result = parent :: change_password();
		
		if($this->_walk_node_objects('change_password') === false)
			return false;
		
		return $result;		
	}

	function change_own_password($password)
	{
		$result = parent :: change_own_password($password);

		if($this->_walk_node_objects('change_own_password', array('password' => $password)) === false)
			return false;
				
		return $result;		
	}

	function generate_password($email)
	{
		$result = parent :: generate_password($email);

		if($this->_walk_node_objects('generate_password', array('email' => $email)) === false)
			return false;
				
		return $result;		
	}

	function activate_password()
	{
		$result = parent :: activate_password();
		
		if($this->_walk_node_objects('activate_password') === false)
			return false;
				
		return $result;		
	}
	
	function _walk_node_objects($function, $params = array())
	{
		foreach(array_keys($this->_node_objects) as $id)
			if(call_user_func_array(array($this->_node_objects[$id], $function), $params)  === false)
				return false;
				
		return true;
	}

}

?>