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
	public $tag = 'locale:LOCALE';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'locale_locale_tag';
} 

register_tag(new locale_locale_tag_info());

class locale_locale_tag extends compiler_directive_tag
{
	function pre_parse()
	{
		if (!isset($this->attributes['name']) || !$this->attributes['name']))
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'name',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

		return PARSER_REQUIRE_PARSING;
	} 

	public function pre_generate($code)
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

		$code->write_php('if ("' . $this->attributes['name']. '" == constant("'. $locale_constant .'")) {');
	} 

	public function post_generate($code)
	{
		$code->write_php('}');
		parent::post_generate($code);
	} 
} 

?>