<?php

class user_not_in_groups_tag_info
{
	var $tag = 'user:NOT_IN_GROUPS';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'user_not_in_groups_tag';
} 

register_tag(new user_not_in_groups_tag_info());

class user_not_in_groups_tag extends compiler_directive_tag
{
	function pre_parse()
	{
		$groups = $this->attributes['groups'];
		if (empty($groups))
		{
			error('MISSINGREQUIREATTRIBUTE', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
					'attribute' => 'groups',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

		return PARSER_REQUIRE_PARSING;
	} 

	function generate_contents(&$code)
	{
		$groups = $this->attributes['groups'];
		
		$code->write_php("if (user :: is_logged_in() && (!user :: is_in_groups('{$groups}'))) {");
			parent :: generate_contents($code);
		$code->write_php("}");
	}
} 

?>