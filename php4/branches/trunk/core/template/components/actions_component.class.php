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
require_once(LIMB_DIR . '/core/template/components/list_component.class.php');
require_once(LIMB_DIR . '/core/lib/http/control_flow.inc.php');
require_once(LIMB_DIR . '/core/lib/util/array_dataset.class.php');
class actions_component extends list_component
{
  var $all_actions = array();

  var $node_id = '';

  function set_actions($all_actions)
  {
    $this->all_actions = $all_actions;
  }

  function set_node_id($node_id)
  {
    $this->node_id = $node_id;
  }

  function prepare()
  {
    $actions = $this->get_actions();

    $this->register_dataset(new array_dataset($actions));

    return parent :: prepare();
  }

  function get_actions()
  {
    if (!count($this->all_actions))
      return array();

    $actions = array();

    foreach($this->all_actions as $action => $params)
    {
      if (isset($params['extra']))
        $action_params = $params['extra'];
      else
        $action_params = array();

      if(isset($params['popup']) && $params['popup'] === true)
          $action_params['popup'] = 1;

      if (isset($params['JIP']) && $params['JIP'] === true)
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