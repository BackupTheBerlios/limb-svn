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
class core_place_holder_tag_info
{
	public $tag = 'core:PLACEHOLDER';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'core_place_holder_tag';
} 

register_tag(new core_place_holder_tag_info());

/**
* Present a named location where content can be inserted at runtime
*/
class core_place_holder_tag extends server_component_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/placeholder_component';
	}

	public function check_nesting_level()
	{
		if ($this->find_parent_by_class('core_place_holder_tag'))
		{
			throw new WactException('bad self nesting', 
					array('tag' => $this->tag,
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
} 

?>