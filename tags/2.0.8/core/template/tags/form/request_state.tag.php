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


class request_state_tag_info
{
	var $tag = 'request_state';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'request_state_tag';
} 

register_tag(new request_state_tag_info());

class request_state_tag extends server_tag_component_tag
{
	var $runtime_component_path = '/core/template/components/form/request_state_component';
	
	function prepare()
	{
		$this->attributes['type'] = 'hidden';		
	}
	
	function get_rendered_tag()
	{
		return 'input';
	}	
} 

?>