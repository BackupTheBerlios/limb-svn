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
class user_logged_in_tag_info
{
	public $tag = 'user:LOGGED_IN';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'user_logged_in_tag';
} 

register_tag(new user_logged_in_tag_info());

class user_logged_in_tag extends compiler_directive_tag
{
	public function generate_contents($code)
	{
		$user = '$' . $code->get_temp_variable();
		$code->write_php("{$user} =& user :: instance();");

		$code->write_php("if ({$user}->is_logged_in()) {");
			parent :: generate_contents($code);
		$code->write_php("}");
	}
} 

?>