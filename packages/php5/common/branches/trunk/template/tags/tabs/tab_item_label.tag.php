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
class tab_item_label_tag_info
{
	public $tag = 'tab_item:label';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'tab_item_label_tag';
} 

register_tag(new tab_item_label_tag_info());

class tab_item_label_tag extends compiler_directive_tag
{  
	public function check_nesting_level()
	{
		if (!$this->parent instanceof tabs_labels_tag)
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'tabs:labels',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	}

	public function pre_parse()
	{
		if (!isset($this->attributes['tab_id']) || !$this->attributes['tab_id'])
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'tab_id',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		}
		
		$this->parent->parent->tabs[] = $this->attributes['tab_id'];

    return PARSER_REQUIRE_PARSING;
	}
	
	public function pre_generate($code)
	{
	  $id = $this->attributes['tab_id'];
	  
		$code->write_html("<td id={$id}>
					<table border='0' cellspacing='0' cellpadding='0' style='height:100%'>
					<tr>
						<td nowrap {$this->parent->parent->tab_class}><a href='JavaScript:void(0);'>");
		
		parent :: pre_generate($code);
	}
	
	public function post_generate($code)
	{
		$code->write_html("</a></td>
					</tr>
					</table>	
				</td>
		");
		
		parent :: post_generate($code);
	}
} 

?>