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
class user_in_groups_tag_info
{
	public $tag = 'user:IN_GROUPS';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'user_in_groups_tag';
} 

register_tag(new user_in_groups_tag_info());

class user_in_groups_tag extends server_component_tag
{
  $this->runtime_component_path = dirname(__FILE__) . '/../../components/simple_authenticator_component';
   
	public function pre_parse()
	{
		if (!isset($this->attributes['groups']) || !$this->attributes['groups'])
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'groups',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 

		return PARSER_REQUIRE_PARSING;
	} 

	public function generate_contents($code)
	{
		$code->write_php('if ' . $this->get_component_ref_code() . '->is_user_in_groups(' . $this->attributes['groups'] .'){");
			parent :: generate_contents($code);
		$code->write_php("}");
	}
} 

?>