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
class tab_item_content_tag_info
{
	public $tag = 'tab_item:content';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'tab_item_content_tag';
} 

register_tag(new tab_item_content_tag_info());

class tab_item_content_tag extends compiler_directive_tag
{  
	public function check_nesting_level()
	{
		if (!$this->parent instanceof tabs_contents_tag)
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'tabs:contents',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	}

	public function pre_parse()
	{
		if (!isset($this->attributes['tab_id']))
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'id',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 	
		if(!in_array($this->attributes['tab_id'], $this->parent->parent->tabs))
		{
			throw new WactException('invalid attribute value', 
					array('tag' => $this->tag,
					'attribute' => 'tab_id',
					'description' => 'tab_id not declared in <tab_item:label> tag',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		}

    return PARSER_REQUIRE_PARSING;
	}
	
	public function pre_generate($code)
	{
	  $id = $this->attributes['tab_id'];
	  
		$code->write_html("<div id='{$id}_content'>");
		
		parent :: pre_generate($code);
	}
	
	public function post_generate($code)
	{
		$code->write_html("</div>");
		
		parent :: post_generate($code);
	}	
} 

?>