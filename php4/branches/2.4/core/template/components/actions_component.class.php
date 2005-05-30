<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://www.limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/template/components/datasource_component.class.php');
class actions_component extends datasource_component 
{
  var $all_actions = array();
  var $node_id = '';

  function &get_dataset()
  {
    $limit = 0;
    $offset = 0;
    $group_id = "JIP";
    
    if(isset($this->parameters['limit']))
      $limit = $this->parameters['limit'];
    if(isset($this->parameters['offset']))
      $offset = $this->parameters['offset'];
    if(isset($this->parameters['group']))
      $group_id = $this->parameters['group'];

    $this->_set_actions();
    $this->_set_node_id();

    $actions = $this->_get_actions_by_group($group_id, $limit, $offset);
    $ds = new array_dataset($actions);

    return $ds;
  }

  function _set_actions($all_actions)
  {
    $this->all_actions = $this->parent->get("actions");
  }

  function _set_node_id($node_id)
  {
    $this->node_id = $this->parent->get("node_id");
  }

  function _get_actions_by_group($group_id, $limit, $offset)
  {
    if (!count($this->all_actions))
      return array();
    $actions = array();

    $current_item = 0;
    $actions_count = 0;
    foreach($this->all_actions as $action => $params)
    {
      if(!isset($params[$group_id]) && $params[$group_id] !== true)
        continue;

      $current_item++;
      $actions_count++;
      if($offset && $current_item <= $offset)
        continue;

      if($limit && $actions_count > $limit)
        break;

      if (isset($params['extra']))
        $action_params = $params['extra'];
      else
        $action_params = array();

      if(isset($params['popup']) && $params['popup'] === true)
          $action_params['popup'] = 1;


      {
        $actions[$action] = $params;
        $actions[$action]['action'] = $action;
        $action_params['action'] = $action;
        $action_params['node_id'] = $this->node_id;

        $actions[$action]['action_href'] = add_url_query_items('/root', $action_params);
      }
    }

    return $actions;
  }
  
}

?>