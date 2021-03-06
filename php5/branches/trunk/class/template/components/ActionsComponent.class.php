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
require_once(LIMB_DIR . '/class/template/components/ListComponent.class.php');
require_once(LIMB_DIR . '/class/etc/limb_util.inc.php');

class ActionsComponent extends ListComponent
{
  protected $all_actions = array();

  protected $node_id;

  public function setActions($all_actions)
  {
    $this->all_actions = $all_actions;
  }

  public function setNodeId($node_id)
  {
    $this->node_id = $node_id;
  }

  public function prepare()
  {
    $actions = $this->getActions();

    if (count($actions))
      $this->registerDataset(new ArrayDataset($actions));

    return parent :: prepare();
  }

  public function getActions()
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