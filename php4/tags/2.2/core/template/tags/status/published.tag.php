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
class status_published_tag_info
{
	var $tag = 'status:PUBLISHED';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'status_published_tag';
} 

register_tag(new status_published_tag_info());

/**
* Defines an action take, should a dataspace variable have been set at runtime.
* The opposite of the core_default_tag
*/
class status_published_tag extends compiler_directive_tag
{
	function pre_generate(&$code)
	{
		parent::pre_generate($code);
		
		$value = 'true';
		if (isset($this->attributes['value']) && !(boolean)$this->attributes['value'])
			$value = 'false';

		$tempvar = $code->get_temp_variable();
		$actions_tempvar = $code->get_temp_variable();
		$code->write_php('$' . $actions_tempvar . ' = ' . $this->get_dataspace_ref_code() . '->get("actions");');

		$code->write_php('if (isset($' . $actions_tempvar . '["publish"]) && isset($' . $actions_tempvar . '["unpublish"])) {');
		$code->write_php('$' . $tempvar . ' = trim(' . $this->get_dataspace_ref_code() . '->get("status"));');
		$code->write_php('if ((boolean)(constant("SITE_OBJECT_PUBLISHED_STATUS") & $' . $tempvar . ') === ' . $value . ') {');
	} 

	function post_generate(&$code)
	{
		$code->write_php('}');
		$code->write_php('}');
		parent::post_generate($code);
	} 
} 

?>