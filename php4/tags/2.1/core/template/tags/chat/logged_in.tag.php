<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: logged_in.tag.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/


class chat_logged_in_tag_info
{
	var $tag = 'chat:LOGGED_IN';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'chat_logged_in_tag';
} 

register_tag(new chat_logged_in_tag_info());

class chat_logged_in_tag extends compiler_directive_tag
{
	function generate_contents(&$code)
	{
		$code->write_php('
		require_once(LIMB_DIR . "/core/model/chat/chat_user.class.php");
		if (chat_user :: is_logged_in()) {');
			parent :: generate_contents($code);
		$code->write_php("}");
	}
} 

?>