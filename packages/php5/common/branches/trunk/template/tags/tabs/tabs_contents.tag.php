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

class tabs_contents_tag_info
{
	var $tag = 'tabs:contents';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'tabs_contents_tag';
} 

register_tag(new tabs_contents_tag_info());

class tabs_contents_tag extends compiler_directive_tag
{  
  /**
	* 
	* @return void 
	* @access protected 
	*/
	function check_nesting_level()
	{
		if (!is_a($this->parent, 'tabs_tag'))
		{
			error('MISSINGENCLOSURE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, 
			array('tag' => $this->tag,
					'enclosing_tag' => 'tabs',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	
	function pre_generate(&$code)
	{
    $code->write_html("
    	<table>
    	<tr>
    		<td height=100% valign=top>
		");
		
		parent :: pre_generate($code);
	}
	
	function post_generate(&$code)
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