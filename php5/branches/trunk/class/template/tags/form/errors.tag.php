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
class form_errors_tag_info
{
	public $tag = 'form:ERRORS';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'form_errors_tag';
} 

register_tag(new form_errors_tag_info());

class form_errors_tag extends server_component_tag
{
  function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/list_component';
	}
	
	public function check_nesting_level()
	{
		if (!$this->find_parent_by_class('form_tag'))
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'form',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		if (!isset($this->attributes['target']))
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'target',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	
	public function generate_contents($code)
	{
		$parent_form = $this->find_parent_by_class('form_tag');
		
		$target = $this->parent->find_child($this->attributes['target']);
		
		$code->write_php($target->get_component_ref_code() . '->register_dataset(' .
			$parent_form->get_component_ref_code() . '->get_error_dataset());');	
			
		parent :: generate_contents($code);
	} 
} 

?>