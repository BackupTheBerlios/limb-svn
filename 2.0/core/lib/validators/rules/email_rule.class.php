<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: email_rule.class.php 410 2004-02-06 10:46:51Z server $
*
***********************************************************************************/ 
require_once(LIMB_DIR . '/core/lib/validators/rules/domain_rule.class.php');

class email_rule extends domain_rule
{
	/**
	* Constructs a email_rule
	* 
	* @param string $ fieldname to validate
	* @param array $ of acceptable values
	* @access public 
	*/
	function email_rule($fieldname)
	{
		parent::domain_rule($fieldname);
	} 

	/**
	* Performs validation of an email user
	* 
	* @access protected 
	* @TODO Verify that this is reasonable:
	*/
	function check_user($value)
	{
		if (!preg_match('/^[a-z0-9]+([_.-][a-z0-9]+)*$/', $value))
		{
			$this->error('EMAIL_INVALID_USER');
		} 
	} 

	/**
	* Performs validation of an email domain
	* 
	* @access protected 
	*/
	function check_domain($value)
	{
		parent :: check($value);
	} 

	/**
	* Performs validation of a single value
	* 
	* @access protected 
	*/
	function check($value)
	{
		if (is_integer(strpos($value, '@')))
		{
			list($user, $domain) = split('@', $value, 2);
			$this->check_user($user);
			$this->check_domain($domain);
		} 
		else
		{
			$this->error('EMAIL_INVALID');
		} 
	} 
} 

?>