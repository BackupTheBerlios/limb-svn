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

class poll_active_tag_info
{
	var $tag = 'poll_active';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'poll_active_tag';
} 

register_tag(new poll_active_tag_info());

/**
* The parent compile time component for lists
*/
class poll_active_tag extends server_component_tag
{
  function poll_active_tag()
  {
    $this->runtime_component_path = dirname(__FILE__) . '/../components/poll_component';
  }
  
	function pre_generate(&$code)
	{
		parent::pre_generate($code);
		
		$code->write_php($this->get_component_ref_code() . '->prepare();');
	} 

	function generate_contents(&$code)
	{		
		$code->write_php('if (' . $this->get_component_ref_code() . '->poll_exists()) {');
		parent :: generate_contents($code);
		$code->write_php('}');
	} 

 
	function &get_dataspace()
	{
		return $this;
	} 

	function get_dataspace_ref_code()
	{
		return $this->get_component_ref_code();
	} 
}

?>