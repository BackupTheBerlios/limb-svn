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
* Used to allow PHP variable references to be copied from straight from the source
* to the compiled template file.
*/
class variable_reference extends compiler_component
{
	/**
	* Reference of variable
	*/
	public $reference;
	/**
	* Scope of variable
	*/
	public $scope;

	/**
	* Generate the code
	*/
	public function generate($code)
	{ 
		// This has to find parental namespaces, not parental components
		switch ($this->scope)
		{
			case '#':
				$context = $this->get_root_dataspace();
				break;
			case '^':
				$context = $this->get_parent_dataspace();
				break;
			case '$':
				$context = $this->get_dataspace();
				break;
		} 
		if ($context != null)
		{
			if (array_key_exists($this->reference, $context->vars))
			{
				$code->write_html($context->vars[$this->reference]);
			} 
			elseif(strpos($this->reference, '[') !== false)
			{	
				$index = addslashes(preg_replace('/^([^\[\]]+)(\[.*\])+$/', "['\\1']\\2", $this->reference));
				$code->write_php('echo ' . $context->get_dataspace_ref_code() . '->get_by_index_string(\'' . $index . '\');');
			}
			else
			{
				$code->write_php('echo ' . $context->get_dataspace_ref_code() . '->get(\'' . $this->reference . '\');');
			} 
		} 
	} 
} 

?>