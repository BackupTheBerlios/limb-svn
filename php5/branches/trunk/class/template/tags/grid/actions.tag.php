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

class grid_actions_tag_info
{
	public $tag = 'grid:actions';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'grid_actions_tag';
} 

register_tag(new grid_actions_tag_info());

class grid_actions_tag extends compiler_directive_tag
{
  protected $_actions = array();

	public function check_nesting_level()
	{
		if (!$this->find_parent_by_class('grid_list_tag'))
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'grid:LIST',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	
	public function pre_generate($code)
	{	  	  
		$grid_tag = $this->find_parent_by_class('grid_list_tag');
		$grid_tag->set_form_required();

	  parent :: pre_generate($code);
	}
	
	public function register_action($action)
	{
		$this->_actions[] = $action;
	}

	public function post_generate($code)
	{
		if(!count($this->_actions))
			parent :: post_generate($code);
		$selector_id = uniqid(''); 
		
		$code->write_html("
		<select id='{$selector_id}'>
        <option value=''>");
		$code->write_php("echo strings :: get('choose_action_for_selected_rows')");        
    $code->write_html("</option>");

		foreach($this->_actions as $option)
		{
			$action_path = $this->_get_action_path($option);
			$code->write_html("<option value='{$action_path}'>");
			if(isset($option['locale_value']))
			{
				$locale_file = '';
				if(isset($option['locale_file']))
					$locale_file = "','{$option['locale_file']}";
				$code->write_php("echo strings :: get('" . $option['locale_value'] . $locale_file ."')");
			}
			else
				$code->write_html($option['name']);
			$code->write_html("</option>");
		}
		$code->write_html("</select>");
		$this->_render_button($code, $selector_id);
    parent :: post_generate($code);
	}

	protected function _render_button($code, $selector_id)
	{
		if (!defined('SUBMIT_GRID_FORM_SCRIPT_LOADED'))
		{
			define('SUBMIT_GRID_FORM_SCRIPT_LOADED', 1);

	    $code->write_html("
	    	<script>
	    		function submit_grid_form(button, selector_id)
	    		{
	    			menu = document.getElementById(selector_id);
	    			action = menu.options[menu.selectedIndex].value;
	    			if(action != '')
	    				submit_form(button.form, action);
	    		}
	    	</script>
	    ");
	  }
    $code->write_html("&nbsp;<input type='button' value=");

		if(isset($this->attributes['locale_value']))
		{
			$locale_file = '';
			if(isset($this->attributes['locale_file']))
				$locale_file = "','{$option['locale_file']}";
			$code->write_php("echo '\'' . strings :: get('" . $this->attributes['locale_value'] . $locale_file ."') . '\''");
		}
		else
			$code->write_html("'" . $option['name'] . "'");
    if(isset($this->attributes['button_class']))
    	$code->write_html(" class='{$this->attributes['button_class']}'");
    	
    $code->write_html(" onclick='submit_grid_form(this, \"{$selector_id}\")'>");
	}

	protected function _get_action_path($option)
	{
		if (!isset($option['path']))
		{
			$action_path = $_SERVER['PHP_SELF'];
			
			if($node_id = request :: instance()->get_attribute('node_id'))
				$action_path .= '?node_id=' . $node_id;
		}
		else
			$action_path = $option['path'];
		
		if (strpos($action_path, '?') === false)
			$action_path .= '?';
		else	
			$action_path .= '&';
		
		if($option['action'])
			$action_path .= 'action=' . $option['action'];
			
		if (isset($option['reload_parent']) && $option['reload_parent'])
			$action_path .= '&reload_parent=1';

		if (isset($option['form_submitted']) && $option['form_submitted'])
			$action_path .= '&grid_form[submitted]=1';

		return $action_path;
	}
} 

?>