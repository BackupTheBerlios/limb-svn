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
require_once(LIMB_DIR . '/class/template/tags/datasource/Datasource.tag.php');

class FetchSubBranchTagInfo
{
	public $tag = 'fetch:SUB_BRANCH';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'fetch_sub_branch_tag';
} 

registerTag(new FetchSubBranchTagInfo());

class FetchSubBranchTag extends DatasourceTag
{	
  function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/fetch_sub_branch_datasource_component';
	}
} 

?>