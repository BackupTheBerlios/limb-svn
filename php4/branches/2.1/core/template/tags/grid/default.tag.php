<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: default.tag.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/

class grid_default_tag_info
{
	var $tag = 'grid:DEFAULT';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'grid_default_tag';
} 

register_tag(new grid_default_tag_info());

class grid_default_tag extends silent_compiler_directive_tag
{
	function check_nesting_level()
	{
		if ($this->find_parent_by_class('grid_default_tag'))
		{
			error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		if (!is_a($this->parent, 'grid_list_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'grid:LIST',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
} 

?>