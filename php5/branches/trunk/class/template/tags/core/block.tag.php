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
class core_block_tag_info
{
	public $tag = 'core:BLOCK';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'core_block_tag';
} 

register_tag(new core_block_tag_info());

class core_block_tag extends server_component_tag
{
  public function __construct()
  {
	  $this->runtime_component_path = dirname(__FILE__) . '/../../components/block_component';
	}

	public function generate_constructor($code)
	{
		parent::generate_constructor($code);
		if (array_key_exists('hide', $this->attributes))
		{
			$code->write_php($this->get_component_ref_code() . '->visible = false;');
		} 
	} 

	public function pre_generate($code)
	{
		parent::pre_generate($code);
		$code->write_php('if (' . $this->get_component_ref_code() . '->is_visible()) {');
	} 

	public function post_generate($code)
	{
		$code->write_php('}');
		parent::post_generate($code);
	} 
} 

?>