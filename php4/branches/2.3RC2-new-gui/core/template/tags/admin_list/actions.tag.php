<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: actions.tag.php 1014 2005-01-12 13:47:44Z moltyaninov $
*
***********************************************************************************/

class  admin_list_actions_tag_info
{
  var $tag = 'admin:list:actions';
  var $end_tag = ENDTAG_REQUIRED;
  var $tag_class = 'admin_list_actions_tag';
}

register_tag(new  admin_list_actions_tag_info());

class  admin_list_actions_tag extends compiler_directive_tag 
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
    $span_id = uniqid('');


    $code->write_html("<script>arr_actions['{$span_id}'] = {");

    foreach($this->actions as $action_name => $action)
    {
      $action_path = $this->get_action_path($action);
      $code->write_html("'{$action_name}':{'href':'{$action_path}', 'name': '");

      if(isset($action['locale_value']))
      {
        $locale_file = '';
        if(isset($action['locale_file']))
          $locale_file = "','{$action['locale_file']}";
        $code->write_php("echo strings :: get('" . $action['locale_value'] . $locale_file ."')");
      }
      else
        $code->write_html($action['name']);

      $code->write_html("'},");
    }
    $code->write_html("'_' : {}}</script>");

    $code->write_html("<span id='{$span_id}' behavior='CDDGridAction' ddalign='vbr'><img alt='' src='/shared/images/marker/1.gif'> ");
    $code->write_php("echo strings :: get('actions_for_selected');");
    $code->write_html("</span>");
    parent :: post_generate($code);
  }

  function get_action_path($option)
  {
    if (!isset($option['path']))
    {
      $action_path = $_SERVER['PHP_SELF'];

      $request = request :: instance();
      if($node_id = $request->get_attribute('node_id'))
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