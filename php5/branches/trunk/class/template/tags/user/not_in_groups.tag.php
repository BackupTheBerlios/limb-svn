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
class user_not_in_groups_tag_info
{
	public $tag = 'user:NOT_IN_GROUPS';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'user_not_in_groups_tag';
} 

register_tag(new user_not_in_groups_tag_info());

class user_not_in_groups_tag extends compiler_directive_tag
{
	public function pre_parse()
	{
		if (!isset($this->attributes['groups']) || !$this->attributes['groups'])
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'groups',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

		return PARSER_REQUIRE_PARSING;
	} 

	public function generate_contents($code)
	{
		$groups = $this->attributes['groups'];
		
		$user = '$' . $code->get_temp_variable();
		$code->write_php("{$user} =& user :: instance();");
		
		$code->write_php("if ({$user}->is_logged_in() && (!{$user}->is_in_groups('{$groups}'))) {");
			parent :: generate_contents($code);
		$code->write_php("}");
	}
} 

?>