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


require_once(LIMB_DIR . '/class/template/tags/form/control_tag.class.php');

class text_area_tag_info
{
	var $tag = 'textarea';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'text_area_tag';
} 

register_tag(new text_area_tag_info());

class text_area_tag extends control_tag
{
  function text_area_tag()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/form/text_area_component';
	}
		
	function generate_contents(&$code)
	{
		$code->write_php($this->get_component_ref_code() . '->render_contents();');
	} 
} 

?>