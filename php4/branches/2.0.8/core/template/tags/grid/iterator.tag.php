<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: item.tag.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/


class grid_iterator_tag_info
{
	var $tag = 'grid:ITERATOR';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'grid_iterator_tag';
} 

register_tag(new grid_iterator_tag_info());

class grid_iterator_tag extends compiler_directive_tag
{
	function check_nesting_level()
	{
		if (!is_a($this->parent, 'grid_list_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'grid:LIST',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 

	function pre_generate(&$code)
	{
		parent::pre_generate($code);
		
		$code->write_php('if (' . $this->get_component_ref_code() . '->next()) {');
	} 

	function generate_contents(&$code)
	{				
		$code->write_php('do { ');
		
		parent :: generate_contents($code);
		
		$code->write_php('} while (' . $this->get_dataspace_ref_code() . '->next());');
	} 

	function post_generate(&$code)
	{
		$code->write_php('}');
		
		parent::post_generate($code);
	} 
} 

?>