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
class hint_content_tag_info
{
	public $tag = 'hint:CONTENT';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'hint_content_tag';
} 

register_tag(new hint_content_tag_info());

class hint_content_tag extends compiler_directive_tag
{
} 

?>