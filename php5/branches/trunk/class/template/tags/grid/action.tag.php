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
class grid_action_tag_info
{
	public $tag = 'grid:action';
	public $end_tag = ENDTAG_FORBIDDEN;
	public $tag_class = 'grid_action_tag';
} 

register_tag(new grid_action_tag_info());

class grid_action_tag extends compiler_directive_tag
{  
	public function check_nesting_level()
	{
		if (!$this->parent instanceof grid_actions_tag)
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'gird:actions',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
 
	function pre_parse()
	{
		$action = array();

		if(!isset($this->attributes['action']) && !isset($this->attributes['shortcut']))
		{
			throw new WactException('missing required attribute', 
					array('tag' => $this->tag,
					'attribute' => 'action or shortcut',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 		

		if(isset($this->attributes['shortcut']))
		{
      $conf = Limb :: toolkit()->getINI('grid_actions.ini');
			$action['action'] = $conf->get_option($this->attributes['shortcut'], 'action');
			$action['path'] = $conf->get_option($this->attributes['shortcut'],  'path');
		}
		else
		{
			$action['action'] = $this->attributes['action'];
	
			if(isset($this->attributes['path']))
				$action['path'] = $this->attributes['locale_value'];
		}

		if(isset($this->attributes['locale_value']))
			$action['locale_value'] = $this->attributes['locale_value'];

		if(isset($this->attributes['locale_file']))
			$action['locale_file'] = $this->attributes['locale_file'];

		if(isset($this->attributes['name']))
			$action['name'] = $this->attributes['name'];

		$this->parent->register_action($action);
		
		return PARSER_REQUIRE_PARSING;
	}
} 

?>