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
class grid_item_tag_info
{
	public $tag = 'grid:ITEM';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'grid_item_tag';
} 

register_tag(new grid_item_tag_info());

class grid_item_tag extends compiler_directive_tag
{
	public function check_nesting_level()
	{
		if (!is_a($this->parent, 'grid_list_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'grid:ITEM',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
} 

?>