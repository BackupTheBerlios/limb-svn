<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: actions.tag.php 916 2004-11-23 09:14:28Z pachanga $
*
***********************************************************************************/

class admin_list_actions_tag_info
{
  var $tag = 'admin:list:actions';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'admin_list_actions_tag';
}

register_tag(new admin_list_actions_tag_info());

class admin_list_actions_tag extends compiler_directive_tag
{
  var $actions = array();

  function check_nesting_level()
  {
    if (!$this->find_parent_by_class('grid_list_tag'))
    {
      error('INVALIDNESTING', __FILE__ . ' : ' . __LINE__ . ' : ' .  __FUNCTION__, array('tag' => $this->tag,
          'enclosing_tag' => 'grid:LIST',
          'file' => $this->source_file,
          'line' => $this->starting_line_no));
    }
  }

  function prepare()
  {
    $grid_tag =& $this->find_parent_by_class('grid_list_tag');
    $grid_tag->set_form_required();

    parent :: prepare();
  }

  function register_action($action)
  {
    $this->actions[] = $action;
  }

  function post_generate(&$code)
  {
    if(!count($this->actions))
      parent :: post_generate($code);

    $buttons_count = 0;
    if(isset($this->attributes['buttons']))
      $buttons_count = (int)$this->attributes['buttons'];
    
    $code->write_html("      
						<table border='0' cellspacing='0' cellpadding='0'>
						<tr>
							<td style='padding:0 6px 0 14px'><img src='/shared/images/icon/common/12/action.gif'></td>
    ");
    
    $i = 0;
    foreach($this->actions as $option)
    {
      $action_path = $this->_get_action_path($option);
      $action_name = $this->_get_action_name($option);
      if($i >= $buttons_count)
      {
        if($i == $buttons_count)
        {
          $code->write_html("<td><select id='" . uniqid('') . "'><option value=''>");
          $code->write_php("echo strings :: get('choose_any')");
          $code->write_html("</option>");
        }
          
        $code->write_html("<option value='{$action_path}' onclick='submit_form(this.form, this.value)'>");
        $code->write_php("echo {$action_name}");
        $code->write_html("</option>");

        if($i == count($this->actions))
          $code->write_html("</select></td>");
      }
      else 
      {
        $code->write_html("<td><button class='button' onclick='submit_form(this.form, \"{$action_path}\");'>");
        $code->write_php("echo {$action_name}");
        $code->write_html("</button></td>");

      }
      $i ++;
    }
    $code->write_html("      
    						</tr>
						</table> 
    ");
    
    parent :: post_generate($code);
  }

  function _get_action_name($action)
  {
    if(isset($action['locale_value']))
    {
      $locale_file = '';
      if(isset($action['locale_file']))
        $locale_file = "','{$action['locale_file']}";
      return "strings :: get('" . $action['locale_value'] . $locale_file ."')";
    }
    else
      return '"' . $action['name'] . '"';
  }
  
  function _get_action_path($action)
  {
    if (!isset($action['path']))
    {
      $action_path = $_SERVER['PHP_SELF'];

      $request = request :: instance();
      if($node_id = $request->get_attribute('node_id'))
        $action_path .= '?node_id=' . $node_id;
    }
    else
      $action_path = $action['path'];

    if (strpos($action_path, '?') === false)
      $action_path .= '?';
    else
      $action_path .= '&';

    if($action['action'])
      $action_path .= 'action=' . $action['action'];

    if (isset($action['reload_parent']) && $action['reload_parent'])
      $action_path .= '&reload_parent=1';

    if (isset($action['form_submitted']) && $action['form_submitted'])
      $action_path .= '&grid_form[submitted]=1';

    return $action_path;
  }
}

?>