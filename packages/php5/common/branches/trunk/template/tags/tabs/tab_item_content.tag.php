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

class tab_item_content_tag_info
{
	var $tag = 'tab_item:content';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'tab_item_content_tag';
} 

register_tag(new tab_item_content_tag_info());

class tab_item_content_tag extends compiler_directive_tag
{  
	/**
	* 
	* @return void 
	* @access protected 
	*/
	protected function check_nesting_level()
	{
		if (!is_a($this->parent, 'tabs_contents_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			array('tag' => $this->tag,
					'enclosing_tag' => 'tabs:contents',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
		
		if (!isset($this->attributes['tab_id']))
		{
			error('ATTRIBUTE_REQUIRED', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			array('tag' => $this->tag,
					'attribute' => 'id',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 	
		if(!in_array($this->attributes['tab_id'], $this->parent->parent->tabs))
		{
			error('ATRRIBUTE_INVALID', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			array('tag' => $this->tag,
					'attribute' => 'tab_id',
					'description' => 'tab_id not declared in <tab_item:label> tag',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));		
		}
		
	}
	
	protected function pre_generate(&$code)
	{
	  $id = $this->attributes['tab_id'];
	  
		$code->write_html("<div id='{$id}_content'>");
		
		parent :: pre_generate($code);
	}
	
	protected function post_generate(&$code)
	{
		$code->write_html("</div>");
		
		parent :: post_generate($code);
	}	
} 

?>