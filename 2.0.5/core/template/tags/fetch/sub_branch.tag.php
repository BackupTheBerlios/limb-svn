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
require_once(LIMB_DIR . 'core/template/tags/data_source/data_source.tag.php');

class fetch_sub_branch_tag_info
{
	var $tag = 'fetch:SUB_BRANCH';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'fetch_sub_branch_tag';
} 

register_tag(new fetch_sub_branch_tag_info());

class fetch_sub_branch_tag extends data_source_tag
{	
	var $runtime_component_path = '/core/template/components/fetch_sub_branch_data_source_component';
} 

?>