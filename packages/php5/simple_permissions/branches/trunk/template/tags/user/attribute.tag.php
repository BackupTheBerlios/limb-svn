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
class user_attribute_tag_info
{
	public $tag = 'user:ATTRIBUTE';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'user_attribute_tag';
} 

register_tag(new user_attribute_tag_info());

class user_attribute_tag extends compiler_directive_tag
{
	public function pre_parse()
	{
		if (!isset($this->attributes['name']) || !$this->attributes['name'])
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'name',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

		return PARSER_REQUIRE_PARSING;
	} 

	public function generate_contents($code)
	{
		$code->write_php("echo LimbToolsBox :: getToolkit()->getUser()->get('{$this->attributes['name']}');");
				
		parent :: generate_contents($code);
	}
} 

?>