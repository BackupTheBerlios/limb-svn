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
require_once(LIMB_DIR . '/class/template/tags/form/control_tag.class.php');

class grid_input_tag_info
{
	public $tag = 'grid:INPUT';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'grid_input_tag';
} 

register_tag(new grid_input_tag_info());

class grid_input_tag extends control_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/grid_input_component';
	}
		
	public function check_nesting_level()
	{
		if (!$this->find_parent_by_class('grid_iterator_tag'))
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'grid:ITERATOR',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 	

		if (!isset($this->attributes['name']))
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'name',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 		
	}

	public function prepare()
	{
		$this->attributes['type'] = 'text';
		
		$grid_tag = $this->find_parent_by_class('grid_list_tag');
		$grid_tag->set_form_required();
		
		parent :: prepare();
	}
		
	public function get_rendered_tag()
	{
		return 'input';
	}	
} 

?>