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

require_once(LIMB_DIR . '/core/lib/error/exception.class.php');

/**
* A class for handling database-related errors. 
*/
class sql_exception extends exception
{
	/**
	* * Information that provides additional information for context of exception (e.g. SQL statement or DSN).
	*/
	var $user_info;

	/**
	* * Native RDBMS error string
	*/
	var $native_error;

	/**
	* Constructs a sql_exception.
	* 
	* @param string $msg Error message
	* @param string $native Native DB error message.
	* @param string $userinfo More info, e.g. the SQL statement or the connection string that caused the error.
	*/
	function sql_exception($code, $msg, $native = null, $userinfo = null)
	{
		if ($native !== null)
		{
			$this->set_native_error($native);
		} 
		if ($userinfo !== null)
		{
			$this->set_user_info($userinfo);
		} 

		parent::exception($code, $msg);
	} 

	/**
	* Sets additional user / debug information for this error.
	* 
	* @param array $info 
	* @return void 
	*/
	function set_user_info($info)
	{
		$this->user_info = $info;
		$this->message .= " [User Info: " . $this->user_info . "]";
	} 

	/**
	* Returns the additional / debug information for this error.
	* 
	* @return array hash of user info properties.
	*/
	function get_user_info()
	{
		return $this->user_info;
	} 

	/**
	* Sets driver native error message.
	* 
	* @param string $info 
	* @return void 
	*/
	function set_native_error($msg)
	{
		$this->native_error = $msg;
		$this->message .= " [Native Error: " . $this->native_error . "]";
	} 

	/**
	* Gets driver native error message.
	* 
	* @return string 
	*/
	function get_native_error()
	{
		return $this->native_error;
	} 
} 
