<?php

class list_default_tag_info
{
	var $tag = 'list:DEFAULT';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'list_default_tag';
} 

register_tag(new list_default_tag_info());

/**
* Default List tag for a list which failed to have any contents
*/
class list_default_tag extends silent_compiler_directive_tag
{
	/**
	* 
	* @return void 
	* @access protected 
	*/
	function check_nesting_level()
	{
		if ($this->find_parent_by_class('list_default_tag'))
		{
			error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		if (!is_a($this->parent, 'list_list_tag') && !is_a($this->parent, 'error_summary_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'list:LIST or ERRORSUMMARY',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
} 

?>