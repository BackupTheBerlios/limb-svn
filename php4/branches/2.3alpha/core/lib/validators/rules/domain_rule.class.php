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
require_once(LIMB_DIR . '/core/lib/validators/rules/single_field_rule.class.php');

/**
* Check for a valid domain name.
*/
class domain_rule extends single_field_rule
{
	/**
	* Constructs a domain_rule
	* 
	* @param string $ fieldname to validate
	* @param array $ of acceptable values
	* @access public 
	*/
	function domain_rule($fieldname)
	{
		parent :: single_field_rule($fieldname);
	} 

	/**
	* Performs validation of a single value
	* 
	* @access protected 
	* @todo Find the RFC that describes valid domain names and post a link to it here.
	*/
	function check($value)
	{ 
		// Check for entirely numberic domains.  Is 666.com valid?
		// Don't check for 2-4 character length on TLD because of things like .local
		// We can't be too restrictive by default.
		if (!preg_match("/^[a-zA-Z0-9.-]+$/i", $value))
		{
			$this->error(strings :: get('BAD_DOMAIN_CHARACTERS', 'error'));
		} 

		if (is_integer(strpos($value, '--', $value)))
		{
			$this->error('BAD_DOMAIN_DOUBLE_HYPHENS');
		} 

		if (0 === strpos($value, '.'))
		{
			$this->error('BAD_DOMAIN_STARTING_PERIOD');
		} 

		if (strlen($value) -1 == strrpos($value, '.'))
		{
			$this->error('BAD_DOMAIN_ENDING_PERIOD');
		} 

		if (is_integer(strpos($value, '..', $value)))
		{
			$this->error('BAD_DOMAIN_DOUBLE_DOTS');
		} 

		$segments = explode('.', $value);
		foreach($segments as $dseg)
		{
			$len = strlen($dseg);
			/* ignore empty segments that're due to other errors */
			if (1 > $len)
			{
				continue;
			} 
			if ($len > 63)
			{
				$this->error('BAD_DOMAIN_SEGMENT_TOO_LARGE',
					array('segment' => $dseg));
			} 
			if ($dseg{$len-1} == '-' || $dseg{0} == '-')
			{
				$this->error('BAD_DOMAIN_HYPHENS', array('segment' => $dseg));
			} 
		} 
	} 
} 
?>