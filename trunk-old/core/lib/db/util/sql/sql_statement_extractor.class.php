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
* Static class for extracting SQL statements from a string or file.
*/
class sql_statement_extractor
{
	var $delimiter = ';';

	/**
	* Get SQL statements from file.
	* 
	* @param string $filename Path to file to read.
	* @return array SQL statements
	*/
	function extract_file($filename)
	{
		$buffer = file_get_contents($filename);
		if ($buffer === false)
		{
			return new exception(LIMB_DB_ERROR, "Unable to read file: " . $filename);
		} 
		return sql_statement_extractor::extract_statements(sql_statement_extractor::get_lines($buffer));
	} 

	/**
	* Extract statements from string.
	* 
	* @param string $txt 
	* @return array 
	*/
	function &extract(&$buffer)
	{
		return sql_statement_extractor::extract_statements(sql_statement_extractor::get_lines($buffer));
	} 

	/**
	* Extract SQL statements from array of lines.
	* 
	* @param array $lines Lines of the read-in file.
	* @return string 
	*/
	function &extract_statements(&$lines)
	{
		$self = &sql_statement_extractor::instance();
		$statements = array();
		$sql = '';

		foreach($lines as $line)
		{
			$line = trim($line);

			if (sql_statement_extractor::starts_with("//", $line) ||
					sql_statement_extractor::starts_with("--", $line) ||
					sql_statement_extractor::starts_with("#", $line))
			{
				continue;
			} 

			if (strlen($line) > 4 && strtoupper(substr($line, 0, 4)) == "REM ")
			{
				continue;
			} 

			$sql .= " " . $line;
			$sql = trim($sql); 
			// SQL defines "--" as a comment to EOL
			// and in Oracle it may contain a hint
			// so we cannot just remove it, instead we must end it
			if (strpos($line, "--") !== false)
			{
				$sql .= "\n";
			} 

			if (sql_statement_extractor::ends_with($self->delimiter, $sql))
			{
				$statements[] = sql_statement_extractor::substring($sql, 0, strlen($sql) - strlen($self->delimiter));
				$sql = "";
			} 
		} 

		return $statements;
	} 
	
	// Some string helper functions
	
	/**
	* * tests if a string starts with a given string
	*/
	function starts_with($check, $string)
	{
		if ($check === "" || $check === $string)
		{
			return true;
		} 
		else
		{
			return (strpos($string, $check) === 0) ? true : false;
		} 
	} 

	/**
	* * tests if a string ends with a given string
	*/
	function ends_with($check, $string)
	{
		if ($check === "" || $check === $string)
		{
			return true;
		} 
		else
		{
			return (strpos(strrev($string), strrev($check)) === 0) ? true : false;
		} 
	} 

	/**
	* a natural way of getting a subtring, php's circular string buffer and strange
	* return values suck if you want to program strict as of C or friends
	*/
	function substring($string, $startpos, $endpos = -1)
	{
		$len = strlen($string);
		$endpos = (int) (($endpos === -1) ? $len-1 : $endpos);
		if ($startpos > $len-1 || $startpos < 0)
		{
			trigger_error("substring(), Startindex out of bounds must be 0<n<$len", E_USER_ERROR);
		} 
		if ($endpos > $len-1 || $endpos < $startpos)
		{
			trigger_error("substring(), Endindex out of bounds must be $startpos<n<" . ($len-1), E_USER_ERROR);
		} 
		if ($startpos === $endpos)
		{
			return (string) $string{$startpos};
		} 
		else
		{
			$len = $endpos - $startpos;
		} 
		return substr($string, $startpos, $len + 1);
	} 

	/**
	* Convert string buffer into array of lines.
	* 
	* @param string $filename 
	* @return array string[] lines of file.
	*/
	function &get_lines($buffer)
	{
		$lines = preg_split("/\r?\n|\r/", $buffer);
		return $lines;
	} 

	/*
  * @private
  */
	function &instance()
	{
		static $instance;

		if ($instance === null)
		{
			$instance = new sql_statement_extractor();
		} 

		return $instance;
	} 
} 
