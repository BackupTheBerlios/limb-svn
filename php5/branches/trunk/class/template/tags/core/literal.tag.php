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
class core_literal_tag_info
{
	public $tag = 'core:LITERAL';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'core_literal_tag';
} 

register_tag(new core_literal_tag_info());

/**
* Prevents a section of the template from being parsed, placing the contents
* directly into the compiled template
*/
class core_literal_tag extends compiler_directive_tag
{
	public function check_nesting_level()
	{
		if ($this->find_parent_by_class('core_literal_tag'))
		{
			throw new WactException('bad self nesting', 
					array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	
	public function pre_parse()
	{
		return PARSER_FORBID_PARSING;
	} 
} 

?>