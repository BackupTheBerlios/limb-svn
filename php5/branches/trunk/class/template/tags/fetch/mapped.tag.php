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
require_once (LIMB_DIR . '/class/template/tags/fetch/one.tag.php');

class fetch_mapped_tag_info
{
	public $tag = 'fetch:MAPPED';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'fetch_mapped_tag';
} 

register_tag(new fetch_mapped_tag_info());

class fetch_mapped_tag extends fetch_one_tag
{	
	public function generate_contents($code)
	{
		$code->write_php($this->get_component_ref_code() . '->fetch_requested_object();');
		
		server_component_tag :: generate_contents($code);
	}	
} 

?>