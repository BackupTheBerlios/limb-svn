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
class grid_default_tag_info
{
	public $tag = 'grid:DEFAULT';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'grid_default_tag';
} 

register_tag(new grid_default_tag_info());

class grid_default_tag extends silent_compiler_directive_tag
{
	public function check_nesting_level()
	{
		if ($this->find_parent_by_class('grid_default_tag'))
		{
			throw new WactException('bad self nesting', 
					array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		if (!$this->parent instanceof grid_list_tag)
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'grid:LIST',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
} 

?>