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


class list_separator_tag_info
{
	var $tag = 'list:SEPARATOR';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'list_separator_tag';
} 

register_tag(new list_separator_tag_info());

/**
* Compile time component for seperators in a list
*/
class list_separator_tag extends silent_compiler_directive_tag
{
	/**
	* 
	* @return void 
	* @access private 
	*/
	function check_nesting_level()
	{
		if ($this->find_parent_by_class('list_separator_tag'))
		{
			error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		if (!is_a($this->parent, 'list_item_tag') && !is_a($this->parent, 'error_summary_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'list:ITEM or ERRORSUMMARY',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
} 

?>