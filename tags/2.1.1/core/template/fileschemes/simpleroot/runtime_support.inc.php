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
* Determines the full path to a compiled template file.
*/
function resolve_template_compiled_file_name($sourcefile)
{	
	return VAR_DIR . '/compiled/' . md5($sourcefile) . '.php';
} 

/**
* Returns the contents of a compiled template file
*/
function read_template_file($file)
{
	return file_get_contents($file);
} 

?>