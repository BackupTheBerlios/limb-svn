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

require_once(LIMB_DIR . '/core/template/tags/form/button.tag.php');

class grid_button_tag_info
{
	var $tag = 'grid:BUTTON';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'grid_button_tag';
} 

register_tag(new grid_button_tag_info());

class grid_button_tag extends button_tag
{
	var $runtime_component_path = '/core/template/components/form/input_submit_component';
		
	function check_nesting_level()
	{
		if (!$this->find_parent_by_class('grid_list_tag'))
		{
			error('INVALIDNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'enclosing_tag' => 'grid:LIST',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		}
		
		if (!isset($this->attributes['path']))
		{
			error('ATTRIBUTE_REQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'path',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	}

	function prepare()
	{
		$grid_tag =& $this->find_parent_by_class('grid_list_tag');
		$grid_tag->set_form_required();
		
		if(isset($this->attributes['form_submitted']))
		{
			$grid_tag->set_form_submitted((boolean)$this->attributes['form_submitted']);
			unset($this->attributes['form_submitted']);
		}

		$this->attributes['type'] = 'button';
		
		$action_path = $this->attributes['path'] . '?';
		
		if(isset($this->attributes['action']))
			$action_path .= 'action=' . $this->attributes['action'];
			
		if (isset($this->attributes['reload_parent']) && $this->attributes['reload_parent'])
		{
			$action_path .= '&reload_parent=1';
			unset($this->attributes['reload_parent']);
		}
		
		$this->attributes['onclick'] = "submit_form('grid_form_{$grid_tag->attributes['id']}', '{$action_path}')";
		
		parent :: prepare();
		
		unset($this->attributes['path']);
		unset($this->attributes['action']);
	}
	
	function get_rendered_tag()
	{
		return 'input';
	}	
} 

?>