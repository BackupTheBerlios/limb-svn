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

class tabs_labels_tag_info
{
	var $tag = 'tabs:labels';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'tabs_labels_tag';
} 

register_tag(new tabs_labels_tag_info());

class tabs_labels_tag extends compiler_directive_tag
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
	  $tabulator_class = $this->parent->tabulator_class;
	  $tab_class = $this->parent->tab_class;
	  
    $code->write_html("
		<table width=100% border=0 cellspacing=0 cellpadding=0 {$tabulator_class}>
		<tr>    
    <tr>
		  <td {$tab_class}>&nbsp;</td>");
	}
	
	function post_generate(&$code)
	{
	  $tab_class = $this->parent->tab_class;
	  
    $code->write_html("<td class=tab width=100%>&nbsp;</td>
			</tr>
			</table>");
	}	
} 

?>