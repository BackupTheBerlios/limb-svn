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
class tabs_contents_tag_info
{
	public $tag = 'tabs:contents';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'tabs_contents_tag';
} 

register_tag(new tabs_contents_tag_info());

class tabs_contents_tag extends compiler_directive_tag
{  
	public function check_nesting_level()
	{
		if (!$this->parent instanceof tabs_tag)
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'tabs',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	
	public function pre_generate($code)
	{
    $code->write_html("
    	<table>
    	<tr>
    		<td height=100% valign=top>
		");
		
		parent :: pre_generate($code);
	}
	
	public function post_generate($code)
	{
	  $tab_class = $this->parent->tab_class;
	  
    $code->write_html("
  		</td>
  	</tr>
  	</table>	
	  ");
	
	  parent :: post_generate($code);
	}		
} 

?>