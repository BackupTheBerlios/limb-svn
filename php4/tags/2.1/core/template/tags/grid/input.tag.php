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

require_once(LIMB_DIR . '/core/template/tags/form/control_tag.class.php');

class grid_input_tag_info
{
	var $tag = 'grid:INPUT';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'grid_input_tag';
} 

register_tag(new grid_input_tag_info());

class grid_input_tag extends control_tag
{
	var $runtime_component_path = '/core/template/components/form/grid_input_component';
		
	function check_nesting_level()
	{
		if (!$this->find_parent_by_class('grid_iterator_tag'))
		{
			error('INVALIDNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'grid:ITERATOR',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 	

		if (!isset($this->attributes['name']))
		{
			error('ATTRIBUTE_REQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'name',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 		
	}

	function prepare()
	{
		$this->attributes['type'] = 'text';
		
		$grid_tag =& $this->find_parent_by_class('grid_list_tag');
		$grid_tag->set_form_required();
		
		parent :: prepare();
	}
		
	function get_rendered_tag()
	{
		return 'input';
	}	
} 

?>