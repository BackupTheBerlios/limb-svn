<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(WACT_ROOT . '/template/template.inc.php');
require_once(WACT_ROOT . '/template/components/list/list.inc.php');
require_once(LIMB_DIR . '/core/etc/limb_util.inc.php');

class ActionsComponent extends ListComponent
{
  var $all_actions = array();

  var $node_id;

  function setActions($all_actions)
  {
    $this->all_actions = $all_actions;
  }

  function setNodeId($node_id)
  {
    $this->node_id = $node_id;
  }

  function prepare()
  {
    $actions = $this->getActions();

    if (count($actions))
      $this->registerDataset(new ArrayDataset($actions));

    return parent :: prepare();
  }

  function getActions()
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

      if(isset($params['popup']) &&  $params['popup'] === true)
          $action_params['popup'] = 1;

      if (isset($params['JIP']) &&  $params['JIP'] === true)
      {
        $actions[$action] = $params;
        $actions[$action]['action'] = $action;
        $action_params['action'] = $action;
        $action_params['node_id'] = $this->node_id;

        $actions[$action]['action_href'] = addUrlQueryItems('/root', $action_params);
      }
    }

    return $actions;
  }
}

?>