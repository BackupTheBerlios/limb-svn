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
class core_set_tag_info
{
	public $tag = 'core:SET';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'core_set_tag';
} 

register_tag(new core_set_tag_info());

/**
* Sets a variable in the runtime dataspace, according the attributes of this
* tag at compile time.
*/
class core_set_tag extends silent_compiler_directive_tag
{
	public function check_nesting_level()
	{
		if ($this->find_parent_by_class('core_set_tag'))
		{
			throw new WactException('bad self nesting', 
					array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	
	public function pre_parse()
	{
		$dataspace = $this->get_dataspace();
		$dataspace->vars += $this->attributes;
		return PARSER_FORBID_PARSING;
	} 
} 

?>