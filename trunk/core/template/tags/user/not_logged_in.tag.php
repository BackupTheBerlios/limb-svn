<?php

class user_not_logged_in_tag_info
{
	var $tag = 'user:NOT_LOGGED_IN';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'user_not_logged_in_tag';
} 

register_tag(new user_not_logged_in_tag_info());

class user_not_logged_in_tag extends compiler_directive_tag
{
	function generate_contents(&$code)
	{
		$code->write_php('if (!user :: is_logged_in()) {');
			parent :: generate_contents($code);
		$code->write_php("}");
	}
} 

?>