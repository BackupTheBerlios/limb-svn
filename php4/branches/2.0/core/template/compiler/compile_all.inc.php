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


require_once(LIMB_DIR . 'core/template/compiler/template_compiler.inc.php');
require_once(LIMB_DIR . 'core/template/compiler/var_file_compiler.inc.php');
require_once(TMPL_FILESCHEME_PATH . 'compiler_support.inc.php');
/**
* Invokes compiling of all templates below the directory where the function
* is called from. This simply calls the compile_entire_file_scheme function
*/
function compile_all() 
{
	compile_entire_file_scheme();
}

?>