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
require_once(LIMB_DIR . '/class/template/tags/datasource/datasource.tag.php');

class fetch_sub_branch_tag_info
{
	public $tag = 'fetch:SUB_BRANCH';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'fetch_sub_branch_tag';
} 

register_tag(new fetch_sub_branch_tag_info());

class fetch_sub_branch_tag extends datasource_tag
{	
  function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/fetch_sub_branch_datasource_component';
	}
} 

?>