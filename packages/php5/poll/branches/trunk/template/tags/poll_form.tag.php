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
	public $tag = 'poll:FORM';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'poll_form_tag';
} 

register_tag(new poll_form_tag_info());

class poll_form_tag extends compiler_directive_tag
{
} 

?>