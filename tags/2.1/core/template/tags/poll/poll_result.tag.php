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


class poll_result_tag_info
{
	var $tag = 'poll:RESULT';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'poll_result_tag';
} 

register_tag(new poll_result_tag_info());

class poll_result_tag extends compiler_directive_tag
{
} 

?>