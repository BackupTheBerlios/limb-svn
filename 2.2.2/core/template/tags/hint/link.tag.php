<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.0x00.ru, mailto: bit@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: locale.tag.php 21 2004-03-05 11:43:13Z server $
*
***********************************************************************************/

class hint_link_tag_info
{
	var $tag = 'hint:LINK';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'hint_link_tag';
} 

register_tag(new hint_link_tag_info());

class hint_link_tag extends compiler_directive_tag
{
} 

?>