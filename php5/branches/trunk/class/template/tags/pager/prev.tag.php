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
class pager_prev_tag_info
{
	public $tag = 'pager:PREV';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'pager_prev_tag';
} 

register_tag(new pager_prev_tag_info());

class pager_prev_tag extends server_component_tag
{
	private $hide_for_current_page;

	function check_nesting_level()
	{
		if ($this->find_parent_by_class('pager_prev_tag'))
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

	public function pre_generate($code)
	{
		$this->hide_for_current_page = array_key_exists('hide_for_current_page', $this->attributes);

		$parent = $this->find_parent_by_class('pager_navigator_tag');
		$code->write_php('if (' . $parent->get_component_ref_code() . '->has_prev()) {');

		parent::pre_generate($code);

		$code->write_php($this->get_component_ref_code() . '->set("href", ' . $parent->get_component_ref_code() . '->get_prev_page_uri());');
		if (!$this->hide_for_current_page)
		{
			$code->write_php('}');
		} 
	} 

	public function post_generate($code)
	{
		if (!$this->hide_for_current_page)
		{
			$parent = $this->find_parent_by_class('pager_navigator_tag');
			$code->write_php('if (' . $parent->get_component_ref_code() . '->has_prev()) {');
		} 
		parent::post_generate($code);

		$code->write_php('}');
	} 

	public function generate_contents($code)
	{
		$parent = $this->find_parent_by_class('pager_navigator_tag');
		
		$code->write_php('if (' . $parent->get_component_ref_code() . '->has_prev()) {');
		
		parent :: generate_contents($code);
		
		$code->write_php('}');
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