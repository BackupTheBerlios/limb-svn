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
require_once(LIMB_DIR . 'class/template/tags/datasource/datasource.tag.php');

class fetch_sub_branch_tag_info
{
	var $tag = 'fetch:SUB_BRANCH';
	var $end_tag = ENDTAG_REQUIRED;
	var $tag_class = 'fetch_sub_branch_tag';
} 

register_tag(new fetch_sub_branch_tag_info());

class fetch_sub_branch_tag extends datasource_tag
{	
  function fetch_sub_branch_tag()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/fetch_sub_branch_datasource_component';
	}
} 

?>