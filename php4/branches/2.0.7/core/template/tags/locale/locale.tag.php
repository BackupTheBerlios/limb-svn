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


class locale_locale_tag_info
{
	var $tag = 'locale:LOCALE';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'locale_locale_tag';
} 

register_tag(new locale_locale_tag_info());


class locale_locale_tag extends compiler_directive_tag
{
	var $name;

	function pre_parse()
	{
		$name = $this->attributes['name'];
		if (empty($name))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'name',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

		$this->name = $name;

		return PARSER_REQUIRE_PARSING;
	} 

	function pre_generate(&$code)
	{
		parent::pre_generate($code);

		if(isset($this->attributes['locale_type']))
		{
			if(strtolower($this->attributes['locale_type']) == 'management')
				$locale_constant = 'MANAGEMENT_LOCALE_ID';	
			else
				$locale_constant = 'CONTENT_LOCALE_ID';	
		}
		else
				$locale_constant = 'CONTENT_LOCALE_ID';	

		$code->write_php('if ("' . $this->name. '" == constant("'. $locale_constant .'")) {');
	} 
	

	function post_generate(&$code)
	{
		$code->write_php('}');
		parent::post_generate($code);
	} 
	
} 

?>