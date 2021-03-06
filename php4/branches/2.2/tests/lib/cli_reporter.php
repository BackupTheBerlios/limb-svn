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

if (! defined('ST_FAILDETAIL_SEPARATOR'))
{
	define('ST_FAILDETAIL_SEPARATOR', "->");
} 

if (! defined('ST_FAILS_RETURN_CODE'))
{
	define('ST_FAILS_RETURN_CODE', 1);
} 

if (version_compare(phpversion(), '4.3.0', '<') ||
		php_sapi_name() == 'cgi')
{
	define('STDOUT', fopen('php://stdout', 'w'));
	define('STDERR', fopen('php://stderr', 'w'));
	register_shutdown_function(
		create_function('', 'fclose(STDOUT); fclose(STDERR); return true;'));
} 

/**
* Minimal command line test displayer. Writes fail details to STDERR. Returns 0
* to the shell if all tests pass, ST_FAILS_RETURN_CODE if any test fails.
*/
class CLIReporter extends TestDisplay
{
	var $faildetail_separator = ST_FAILDETAIL_SEPARATOR;

	function CLIReporter($faildetail_separator = null)
	{
		$this->TestDisplay();
		if (! is_null($faildetail_separator))
		{
			$this->setFailDetailSeparator($faildetail_separator);
		} 
	} 

	function setFailDetailSeparator($separator)
	{
		$this->faildetail_separator = $separator;
	} 

	/**
	* Return a formatted faildetail for printing.
	*/
	function &_paintTestFailDetail(&$message)
	{
		$buffer = '';
		$faildetail = $this->getTestList();
		array_shift($faildetail);
		$buffer .= implode($this->faildetail_separator, $faildetail);
		$buffer .= $this->faildetail_separator . "$message\n";
		return $buffer;
	} 

	/**
	* Paint fail faildetail to STDERR.
	*/
	function paintFail($message)
	{
		parent::paintFail($message);
		fwrite(STDERR, 'FAIL' . $this->faildetail_separator . $this->_paintTestFailDetail($message));
	} 

	/**
	* Paint exception faildetail to STDERR.
	*/
	function paintException($message)
	{
		parent::paintException($message);
		fwrite(STDERR, 'EXCEPTION' . $this->faildetail_separator . $this->_paintTestFailDetail($message));
	} 

	/**
	* Paint a footer with test case name, timestamp, counts of fails and
	* exceptions.
	*/
	function paintFooter($test_name)
	{
		$buffer = $this->getTestCaseProgress() . '/' . $this->getTestCaseCount() . ' test cases complete: ';

		if (0 < ($this->getFailCount() + $this->getExceptionCount()))
		{
			$buffer .= $this->getPassCount() . " passes";
			if (0 < $this->getFailCount())
			{
				$buffer .= ", " . $this->getFailCount() . " fails";
			} 
			if (0 < $this->getExceptionCount())
			{
				$buffer .= ", " . $this->getExceptionCount() . " exceptions";
			} 
			$buffer .= ".\n";
			fwrite(STDOUT, $buffer);
			exit(ST_FAILS_RETURN_CODE);
		} 
		else
		{
			fwrite(STDOUT, $buffer . $this->getPassCount() . " passes.\n");
		} 
	} 
} 

?>