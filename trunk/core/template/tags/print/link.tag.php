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

class print_link_tag_info
{
	var $tag = 'print:LINK';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'print_link_tag';
} 

register_tag(new print_link_tag_info());

class print_link_tag extends compiler_directive_tag
{
	function generate_contents(&$code)
	{
		$mapped = '$' . $code->get_temp_variable();
		
		$code->write_php("{$mapped} = fetch_requested_object();");

		$code->write_php("if(isset({$mapped}['actions']) && array_key_exists('print_version', {$mapped}['actions'])){");
		
		$code->write_php($this->get_dataspace_ref_code() . "->set('link', {$mapped}['path'] . '?action=print_version');");
		
		parent :: generate_contents($code);
		
		$code->write_php('}');
	}
} 

?>