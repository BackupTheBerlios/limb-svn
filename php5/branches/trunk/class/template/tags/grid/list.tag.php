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
class grid_list_tag_info
{
	public $tag = 'grid:LIST';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'grid_list_tag';
} 

register_tag(new grid_list_tag_info());

/**
* The parent compile time component for lists
*/
class grid_list_tag extends server_component_tag
{
	protected $has_form = false;
	
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/list_component';
	}	
	
	public function pre_generate($code)
	{
		$code->write_php($this->get_component_ref_code() . '->prepare();');
		
		parent :: pre_generate($code);

		if ($this->has_form)
		{
			$code->write_html('<form name="grid_form" id="grid_form_'. $this->get_server_id() .'" method="post">');
		}

		$code->write_php('if (' . $this->get_dataspace_ref_code() . '->get_total_row_count()){');
	} 

	public function post_generate($code)
	{
		$code->write_php('} else {');
		
		if ($default = $this->find_immediate_child_by_class('grid_default_tag'))
			$default->generate_now($code);
			
		$code->write_php('}');

		if ($this->has_form)
		{
			$code->write_html('</form>');
		}	
		
		parent :: post_generate($code);
	} 

	public function get_dataspace()
	{
		return $this;
	} 

	public function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code() . '->dataset';
	} 
	
	public function set_form_required($status=true)
	{
		$this->has_form = $status;
	}	
} 

?>