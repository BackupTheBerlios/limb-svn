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


class order_tag_info
{
	var $tag = 'order';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'order_tag';
} 

register_tag(new order_tag_info());

class order_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/order_component';

	function pre_generate(&$code)
	{
		parent::pre_generate($code);
				
		$code->write_php($this->get_component_ref_code() . "->import(" . $this->parent->get_dataspace_ref_code() . "->export());\n");
			
		$code->write_php($this->get_component_ref_code() . '->prepare();'."\n");
	} 

	function &get_dataspace()
	{
		return $this;
	} 

	function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code();
	} 
} 

?>