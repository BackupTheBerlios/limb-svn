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


class core_place_holder_tag_info
{
	var $tag = 'core:PLACEHOLDER';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'core_place_holder_tag';
} 

register_tag(new core_place_holder_tag_info());

/**
* Present a named location where content can be inserted at runtime
*/
class core_place_holder_tag extends server_component_tag
{
	var $runtime_component_path = '/core/template/components/placeholder_component';

	/**
	* 
	* @return void 
	* @access protected 
	*/
	function check_nesting_level()
	{
		if ($this->find_parent_by_class('core_place_holder_tag'))
		{
			error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
} 

?>