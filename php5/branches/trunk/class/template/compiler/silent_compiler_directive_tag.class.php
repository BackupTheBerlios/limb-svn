<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/

/**
* Silent compiler directive tags are instructions for the compiler and do
* not have a corresponding runtime component, nor do they normally generate
* output into the compiled template.
*/
abstract class silent_compiler_directive_tag extends compiler_component
{
	/**
	* Does nothing -  silent_compiler_directive_tags do not generate 
	* during construction of the compiled template
	*/
	public function generate($code)
	{ 
		// Silent Compiler Directives do not generate their contents during the
		// normal generation sequence.
	} 

	/**
	* Results in all components registered as children of the instance of this
	* component having their generate() methods called
	*/
	public function generate_now($code)
	{
		return parent :: generate($code);
	} 
} 

?>