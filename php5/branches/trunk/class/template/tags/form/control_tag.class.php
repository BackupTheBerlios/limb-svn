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
/**
* Ancester tag class for input controls
*/
abstract class control_tag extends server_tag_component_tag
{
	public function get_server_id()
	{
		if (!empty($this->attributes['id']))
		{
			return $this->attributes['id'];
		} 
		elseif (!empty($this->server_id))
		{
			return $this->server_id;
		} 
		else
		{
			$this->server_id = get_new_server_id();
			return $this->server_id;
		} 
	} 

	public function check_nesting_level()
	{
		if ($this->find_parent_by_class(get_class($this)))
		{
			throw new WactException('bad self nesting', 
					array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		if (!$this->find_parent_by_class('form_tag'))
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'form',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	
	public function generate_constructor($code)
	{
		parent :: generate_constructor($code);
						
		if (array_key_exists('display_name', $this->attributes))
		{
			$code->write_php($this->get_component_ref_code() . '->display_name = \'' . $this->attributes['display_name'] . '\';');
		unset($this->attributes['display_name']);
		} 
	} 
		
	public function post_generate($code)
	{
		parent :: post_generate($code);
		
		$code->write_php($this->get_component_ref_code() . '->render_js_validation();');
		$code->write_php($this->get_component_ref_code() . '->render_errors();');
	}
} 

?>