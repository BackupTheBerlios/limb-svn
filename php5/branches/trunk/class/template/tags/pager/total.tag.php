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
class pager_total_count_tag_info
{
	public $tag = 'pager:TOTAL';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'pager_total_count_tag';
} 

register_tag(new pager_total_count_tag_info());

/**
* Compile time component for seperators in a Pager
*/
class pager_total_count_tag extends server_component_tag
{
	public function check_nesting_level()
	{
		if (!$this->find_parent_by_class('pager_navigator_tag'))
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'pager:navigator',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 

	public function pre_generate($code)
	{
		$parent = $this->find_parent_by_class('pager_navigator_tag');
		parent::pre_generate($code);

		$code->write_php($this->get_component_ref_code() . '->set("number", ' . $parent->get_component_ref_code() . '->get_total_items());');
		$code->write_php($this->get_component_ref_code() . '->set("pages_count", ' . $parent->get_component_ref_code() . '->get_pages_count());');
		$code->write_php($this->get_component_ref_code() . '->set("more_than_one_page", ' . $parent->get_component_ref_code() . '->has_more_than_one_page());');
	} 

	public function get_dataspace()
	{
		return $this;
	}
	
	public function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code();
	} 
} 

?>