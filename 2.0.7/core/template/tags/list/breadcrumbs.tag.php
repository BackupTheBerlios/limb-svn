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


class list_breadcrums_tag_info
{
	var $tag = 'list:BREADCRUMBS';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'list_breadcrumbs_tag';
} 

register_tag(new list_breadcrums_tag_info());

/**
* Compile time component for seperators in a list
*/
class list_breadcrumbs_tag extends compiler_directive_tag
{
	/**
	* 
	* @return void 
	* @access private 
	*/
	function check_nesting_level()
	{
		if ($this->find_parent_by_class('list_breadcrumbs_tag'))
		{
			error('BADSELFNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		if (!is_a($this->parent, 'list_item_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'list:ITEM',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	}
	
	function generate_contents(&$code)
	{	
		if (array_key_exists('common', $this->attributes))
		{
			$code->write_php('if (!(' . $this->get_dataspace_ref_code() . '->get("is_last"))) {');
			parent :: generate_contents($code);
			$code->write_php('}');
		}
		elseif (array_key_exists('last', $this->attributes))	
		{
			$code->write_php('if ((' . $this->get_dataspace_ref_code() . '->get("is_last"))) {');
			parent :: generate_contents($code);
			$code->write_php('}');
		}
		
	} 
} 

?>