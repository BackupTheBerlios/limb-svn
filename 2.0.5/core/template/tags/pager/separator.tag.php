<?php

class pager_separator_tag_info
{
	var $tag = 'pager:SEPARATOR';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'pager_separator_tag';
} 

register_tag(new pager_separator_tag_info());

/**
* Compile time component for seperators in a Pager
*/
class pager_separator_tag extends silent_compiler_directive_tag
{
	/**
	* 
	* @return void 
	* @access private 
	*/
	function check_nesting_level()
	{
		if ($this->find_parent_by_class('pager_separator_tag'))
		{
			error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		if (!$this->find_parent_by_class('pager_navigator_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'pager:navigator',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
} 

?>