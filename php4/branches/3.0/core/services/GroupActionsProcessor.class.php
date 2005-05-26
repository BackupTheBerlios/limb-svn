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
require_once(LIMB_DIR . '/core/etc/limb_util.inc.php');

class GroupActionsProcessor
{
  var $group_name;

  function GroupActionsProcessor($group_name)
  {
    $this->group_name = $group_name;
  }

  function process(&$object)
  {
    if (!$actions = $object->get('actions'))
      return;

    $grouped_actions = array();
    foreach($actions as $key => $action)
    {
      if(!isset($action[$this->group_name]) || !$action[$this->group_name])
        continue;

      $grouped_actions[$key] = $action;
      $grouped_actions[$key]['name'] = $key;
    }

    $object->set($this->group_name . '_actions', $grouped_actions);
  }
}

?>
