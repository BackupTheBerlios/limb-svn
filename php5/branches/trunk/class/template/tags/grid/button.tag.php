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
require_once(LIMB_DIR . '/class/template/tags/form/button.tag.php');

class grid_button_tag_info
{
	public $tag = 'grid:BUTTON';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'grid_button_tag';
} 

register_tag(new grid_button_tag_info());

class grid_button_tag extends button_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/grid_button_component';
	}
		
	public function check_nesting_level()
	{
		if (!$this->find_parent_by_class('grid_list_tag'))
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'grid:LIST',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		}
	}

	public function prepare()
	{
		$grid_tag = $this->find_parent_by_class('grid_list_tag');
		$grid_tag->set_form_required();
		
		$this->attributes['type'] = 'button';
		
		$this->attributes['onclick'] = '';
		
		if(isset($this->attributes['form_submitted']) && (boolean)$this->attributes['form_submitted'])
		{
			$this->attributes['onclick'] .= "add_form_hidden_parameter(this.form, 'grid_form[submitted]', 1);";
		unset($this->attributes['form_submitted']);
		}
		
		parent :: prepare();		
	}
	
	public function get_rendered_tag()
	{
		return 'input';
	}	
} 

?>