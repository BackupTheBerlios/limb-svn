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

	public function generate_contents($code)
	{
		$user_methods = get_class_methods('user');
		
		$user = '$' . $code->get_temp_variable();

		if(in_array('get_'. $this->attributes['name'], $user_methods))
		{
			$code->write_php("{$user} = user :: instance();");
			$code->write_php("echo {$user}->get_{$this->attributes['name']}();");
		}
				
		parent :: generate_contents($code);
	}
} 

?>