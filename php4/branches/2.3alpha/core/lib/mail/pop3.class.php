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
* POP3 Access Class
*  +----------------------------- IMPORTANT ------------------------------+
*  | Uses native php extension IMAP   																		|
*  +----------------------------------------------------------------------+
*/

class pop3 
{
	var $_connection;
	var $_user;
	var $_host;
	var $_password;

	var $_is_error = false;
	var $_errors = array();
	
	function pop3()
	{
	}
	
	function login($email = '', $password = '')
	{
		$this->_set_user_data($email, $password);
		return $this->_connect();
	}

	function logout()
	{
		$this->_disconnect();
	}
	
	function _set_user_data($email = '', $password = '')
	{
		if(empty($email) || empty($password))
		{
			$this->set_error('empty connection params');
			return;
		}

		$arr = explode('@', $email);
		$this->_user = trim(stripslashes($arr[0]));
		$this->_host = trim(stripslashes($arr[1]));
		$this->_password = trim(stripslashes($password));
	}

	function set_error($err = '')
	{
		$this->_is_error = true;
		if(!empty($err))
			$this->_errors[] = $err;
	}
	
	function is_error()
	{
		return $this->_is_error;
	}

	function get_error()
	{
		return $this->_errors;
	}

}
?>