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
require_once(LIMB_DIR . '/core/model/site_objects/user_object.class.php');

class composite_user_object extends user_object
{
	var $_node_objects = array();
	
	function composite_user_object()
	{
		parent :: user_object();
		
		$this->_initialize_node_objects();
	}
	
	function import_attributes($attributes, $merge=true)
	{
		parent :: import_attributes($attributes, $merge);
		
		$this->_walk_node_objects('import_attributes', array('attributes' => $attributes, 'merge' => $merge));		
	}
	
	function _register_node_object(&$object)
	{
		$this->_node_objects[] =& $object;
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
		if(($result = parent :: change_password()) === false)
			return false;
		
		if($this->_walk_node_objects('change_password') === false)
			return false;
		
		return true;		
	}

	function change_own_password($password)
	{
		if(($result = parent :: change_own_password($password)) === false)
			return false;

		if($this->_walk_node_objects('change_own_password', array('password' => $password)) === false)
			return false;
				
		return true;
	}

	function generate_password($email, &$new_non_crypted_password)
	{
		if(($result = parent :: generate_password($email, $new_non_crypted_password)) === false)
			return false;

		if($this->_walk_node_objects('generate_password', 
				array('email' => $email, 'new_password' => $new_non_crypted_password)) === false)
			return false;
				
		return $result;		
	}

	function activate_password()
	{
		if(($result = parent :: activate_password()) === false)
			return false;
		
		if($this->_walk_node_objects('activate_password') === false)
			return false;
				
		return $result;		
	}
	
	function login($login, $password)
	{
		if(($result = parent :: login($login, $password)) === false)
			return false;
			
		if($this->_walk_node_objects('login', array('login' => $login, 'password' => $password)) === false)
			return false;			
			
		return true;
	}

	function logout()
	{
		if(($result = parent :: logout()) === false)
			return false;
			
		if($this->_walk_node_objects('logout') === false)
			return false;
			
		return true;
	}
	
	function _walk_node_objects($function, $params = array())
	{
		foreach(array_keys($this->_node_objects) as $id)
			if(call_user_func_array(array(&$this->_node_objects[$id], $function), $params)  === false)
				return false;
				
		return true;
	}
	
	function & _initialize_node_objects()
	{
	}
}

?>