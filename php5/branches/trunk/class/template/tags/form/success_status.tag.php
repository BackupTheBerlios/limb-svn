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
class form_success_status_tag_info
{
	public $tag = 'form:SUCCESS_STATUS';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'success_status_tag';
} 

register_tag(new form_success_status_tag_info());

class success_status_tag extends compiler_directive_tag
{
	public function check_nesting_level()
	{
		if (!$this->find_parent_by_class('form_status_tag'))
		{
			throw new WactException('bad self nesting', 
					array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
} 

?>