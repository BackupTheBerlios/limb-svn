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


class poll_form_tag_info
{
	var $tag = 'poll:FORM';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'poll_form_tag';
} 

register_tag(new poll_form_tag_info());

class poll_form_tag extends compiler_directive_tag
{
} 

?>