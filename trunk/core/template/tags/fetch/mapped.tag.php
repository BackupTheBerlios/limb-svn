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


require_once (LIMB_DIR . '/core/template/tags/fetch/one.tag.php');

class fetch_mapped_tag_info
{
	var $tag = 'fetch:MAPPED';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'fetch_mapped_tag';
} 

register_tag(new fetch_mapped_tag_info());

class fetch_mapped_tag extends fetch_one_tag
{	
	function generate_contents(&$code)
	{
		$list_child =& $this->find_immediate_child_by_class('fetch_list_tag');
		if ($list_child)
		{
			$code->write_php($list_child->get_component_ref_code() . '->set_path($_SERVER["PHP_SELF"]);');
		}
					
		$code->write_php($this->get_component_ref_code() . '->fetch_requested_object();');
		
		server_component_tag :: generate_contents($code);
	}	
} 

?>