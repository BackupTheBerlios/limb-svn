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
class phpbb_session_id_tag_info
{
	var $tag = 'phpbb:SESSION_ID';
	var $end_tag = ENDTAG_FORBIDDEN;
	var $tag_class = 'phpbb_session_id_tag';
} 

register_tag(new phpbb_session_id_tag_info());

class phpbb_session_id_tag extends compiler_directive_tag
{
	function generate_contents(&$code)
	{
		$code->write_php('echo session :: get("phpbb_sid")');
	} 
} 

?>