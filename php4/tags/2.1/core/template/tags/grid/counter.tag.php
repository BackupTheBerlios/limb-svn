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

class grid_counter_tag_info
{
	var $tag = 'grid:COUNTER';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'grid_counter_tag';
} 

register_tag(new grid_counter_tag_info());

class grid_counter_tag extends compiler_directive_tag
{
	function check_nesting_level()
	{
		if (!is_a($this->parent, 'grid_iterator_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'grid:ITERATOR',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 

	function generate_contents(&$code)
	{
	  $grid = $this->find_parent_by_class('grid_list_tag');
	   
		$code->write_php('echo ' . $grid->get_component_ref_code() . '->get_counter();');
	} 
} 

?>