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
class print_link_tag_info
{
	public $tag = 'print:LINK';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'print_link_tag';
} 

register_tag(new print_link_tag_info());

class print_link_tag extends compiler_directive_tag
{
	public function generate_contents($code)
	{
		$mapped = '$' . $code->get_temp_variable();

		$code->write_php("{$mapped} = LimbToolsBox :: getToolkit()->getFetcher()->fetch_requested_object(LimbToolsBox :: getToolkit()->getRequest());");

		$code->write_php("if(isset({$mapped}['actions']) && array_key_exists('print_version', {$mapped}['actions'])){");

		$code->write_php($this->get_dataspace_ref_code() . "->set('link', {$mapped}['path'] . '?action=print_version');");

		parent :: generate_contents($code);

		$code->write_php('}');
	}
} 

?>