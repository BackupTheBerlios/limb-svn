<?php
require_once(TMPL_FILESCHEME_PATH . 'compiler_support.inc.php');
// Line breaks in the file must match the line breaks used by the host OS
// Now that this is done at compile time, many other attributes are available.
/**
* Parses a var file into a data structure. Used in conjunction with an
* Importtag
*/
function parse_var_file($filename)
{
	$result = array();

	$raw_lines = file($filename);

	while (list(, $line) = each($raw_lines))
	{
		$equal_pos = strpos($line, '=');
		if ($equal_pos === false)
		{
			$result[trim($line)] = null;
		} 
		else
		{
			$key = trim(substr($line, 0, $equal_pos));
			if (strlen($key) > 0)
			{
				$result[$key] = trim(substr($line, $equal_pos + 1));
			} 
		} 
	} 
	return $result;
} 

/**
* Compiles a var file and calls write_template_file
*/
function compile_var_file($filename)
{
	$destfile = resolve_template_compiled_file_name($filename, TMPL_IMPORT);
	$sourcefile = resolve_template_source_file_name($filename, TMPL_IMPORT);
	if (empty($sourcefile))
	{
		error('MISSINGFILE2', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('srcfile' => $filename));
	} 

	$text = serialize(parse_var_file($sourcefile));

	write_template_file($destfile, $text);
} 

?>