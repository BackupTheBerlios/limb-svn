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
class form_error_status_tag_info
{
	public $tag = 'form:ERROR_STATUS';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'error_status_tag';
} 

register_tag(new form_error_status_tag_info());

class error_status_tag extends compiler_directive_tag
{
	public function check_nesting_level()
	{
		if (!$this->find_parent_by_class('form_status_tag'))
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'form_status',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
} 

?>