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
* Determines the full path to a source template file.
*/
function resolve_template_source_file_name($file)
{
	if (defined('CONTENT_LOCALE_ID'))
		$locale = '_' . CONTENT_LOCALE_ID . '/';
	else
		$locale = '_' . DEFAULT_CONTENT_LOCALE_ID . '/';
	
  if(file_exists(PROJECT_DIR . '/design/main/templates/' . $locale. $file))	//fix this!!!
  	return PROJECT_DIR . '/design/main/templates/' . $locale. $file;
  
  if(file_exists(PROJECT_DIR . '/design/main/templates/' . $file))
  	return PROJECT_DIR . '/design/main/templates/' . $file;
  	
	if(file_exists(LIMB_DIR . '/design/main/templates/' . $locale. $file))
		return LIMB_DIR . '/design/main/templates/' . $locale. $file;

	if(file_exists(LIMB_DIR . '/design/main/templates/' . $file))
		return LIMB_DIR . '/design/main/templates/' . $file;

	if(file_exists(LIMB_DIR . '/design/default/templates/' . $locale. $file))
		return LIMB_DIR . '/design/default/templates/' . $locale. $file;

	if(file_exists(LIMB_DIR . '/design/default/templates/' . $file))
		return LIMB_DIR . '/design/default/templates/' . $file;
	
	if (file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/templates/' . $locale. $file))
		return dirname($_SERVER['SCRIPT_FILENAME']) . '/templates/' . $locale. $file;

	if (file_exists(dirname($_SERVER['SCRIPT_FILENAME']) . '/templates/' . $file))
		return dirname($_SERVER['SCRIPT_FILENAME']) . '/templates/' . $file;
	
	return null;	
} 

/**
* Compiles all source templates below the source scheme directory
* including subdirectories
* 
* @param string $ root directory name
* @param string $ path relative to root
* @return void 
* @access protected 
*/
function recursive_compile_all($root, $path)
{
	if ($dh = opendir($root . $path))
	{
		while (($file = readdir($dh)) !== false)
		{
			if (substr($file, 0, 1) == '.')
			{
				continue;
			} 
			if (is_dir($root . $path . $file))
			{
				recursive_compile_all($root, $path . $file . '/');
				continue;
			} 
			if (substr($file, -5, 5) == '.html')
			{
				compile_template_file($path . $file);
			} 
			else if (substr($file, -5, 5) == '.vars')
			{
				compile_var_file($path . $file);
			} 
		} 
		closedir($dh);
	} 
} 

/**
* Writes a compiled template file
* 
* @param string $ filename
* @param string $ content to write to the file
* @return void 
* @access protected 
*/
function write_template_file($file, $data)
{
	if(!is_dir(dirname($file)))
		dir :: mkdir(dirname($file), 0777, true);
	
	$fp = fopen($file, "wb");
	if (fwrite($fp, $data, strlen($data)))
	{
		fclose($fp);
	} 
} 

?>