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

class GridActionsTagInfo
{
	public $tag = 'grid:actions';
	public $end_tag = ENDTAG_REQUIRED;
	public $tag_class = 'grid_actions_tag';
} 

registerTag(new GridActionsTagInfo());

class GridActionsTag extends CompilerDirectiveTag
{
  protected $_actions = array();

	public function checkNestingLevel()
	{
		if (!$this->findParentByClass('grid_list_tag'))
		{
			throw new WactException('missing enclosure', 
					array('tag' => $this->tag,
					'enclosing_tag' => 'grid:LIST',
					'file' => $this->source_file,
					'line' => $this->starting_line_no));
		} 
	} 
	
	public function preGenerate($code)
	{	  	  
		$grid_tag = $this->findParentByClass('grid_list_tag');
		$grid_tag->setFormRequired();

	  parent :: preGenerate($code);
	}
	
	public function registerAction($action)
	{
		$this->_actions[] = $action;
	}

	public function postGenerate($code)
	{
		if(!count($this->_actions))
			parent :: postGenerate($code);
		$selector_id = uniqid(''); 
		
		$code->writeHtml("
		<select id='{$selector_id}'>
        <option value=''>");
		$code->writePhp("echo strings :: get('choose_action_for_selected_rows')");        
    $code->writeHtml("</option>");

		foreach($this->_actions as $option)
		{
			$action_path = $this->_getActionPath($option);
			$code->writeHtml("<option value='{$action_path}'>");
			if(isset($option['locale_value']))
			{
				$locale_file = '';
				if(isset($option['locale_file']))
					$locale_file = "','{$option['locale_file']}";
				$code->writePhp("echo strings :: get('" . $option['locale_value'] . $locale_file ."')");
			}
			else
				$code->writeHtml($option['name']);
			$code->writeHtml("</option>");
		}
		$code->writeHtml("</select>");
		$this->_renderButton($code, $selector_id);
    parent :: postGenerate($code);
	}

	protected function _renderButton($code, $selector_id)
	{
		if (!defined('SUBMIT_GRID_FORM_SCRIPT_LOADED'))
		{
			define('SUBMIT_GRID_FORM_SCRIPT_LOADED', 1);

	    $code->writeHtml("
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
    $code->writeHtml("&nbsp;<input type='button' value=");

		if(isset($this->attributes['locale_value']))
		{
			$locale_file = '';
			if(isset($this->attributes['locale_file']))
				$locale_file = "','{$option['locale_file']}";
			$code->writePhp("echo '\'' . strings :: get('" . $this->attributes['locale_value'] . $locale_file ."') . '\''");
		}
		else
			$code->writeHtml("'" . $option['name'] . "'");
    if(isset($this->attributes['button_class']))
    	$code->writeHtml(" class='{$this->attributes['button_class']}'");
    	
    $code->writeHtml(" onclick='submitGridForm(this, \"{$selector_id}\")'>");
	}

	protected function _getActionPath($option)
	{
		if (!isset($option['path']))
		{
			$action_path = $_SERVER['PHP_SELF'];
			
			if($node_id = Limb :: toolkit()->getRequest()->getAttribute('node_id'))
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
			
		if (isset($option['reload_parent']) &&  $option['reload_parent'])
			$action_path .= '&reload_parent=1';

		if (isset($option['form_submitted']) &&  $option['form_submitted'])
			$action_path .= '&grid_form[submitted]=1';

		return $action_path;
	}
} 

?>