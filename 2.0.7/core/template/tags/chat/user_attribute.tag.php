<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: attribute.tag.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/

require_once(LIMB_DIR . 'core/model/chat/chat_user.class.php');

class chat_user_attribute_tag_info
{
	var $tag = 'chat:USER_ATTRIBUTE';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'chat_user_attribute_tag';
} 

register_tag(new chat_user_attribute_tag_info());

class chat_user_attribute_tag extends compiler_directive_tag
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

		return PARSER_REQUIRE_PARSING;
	} 

	function generate_contents(&$code)
	{
		$code->write_php("echo chat_user :: get_user_attribute('{$this->attributes['name']}');");
				
		parent :: generate_contents($code);
	}
} 

?>