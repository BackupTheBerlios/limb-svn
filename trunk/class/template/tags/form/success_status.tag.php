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


class form_success_status_tag_info
{
	var $tag = 'form:SUCCESS_STATUS';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'success_status_tag';
} 

register_tag(new form_success_status_tag_info());

class success_status_tag extends compiler_directive_tag
{
	function check_nesting_level()
	{
		if (!$this->find_parent_by_class('form_status_tag'))
		{
			error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
} 

?>