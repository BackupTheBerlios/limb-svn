<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: support@limb-project.com
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id: SimpleAuthorizer.class.php 1032 2005-01-18 15:43:46Z pachanga $
*
***********************************************************************************/
require_once(LIMB_DIR . '/core/services/Service.class.php');

class SimpleACLAuthorizer// implements Authorizer
{
  var $policy = array();

  function attachPolicy($path, $group_name, $access)
  {
    $this->policy[] = array($path, $group_name, $access);
  }

  function canDo($action_name, $path, $service_name)
  {
    $actions = $this->getAccessibleActions($path, $service_name);

    return isset($actions[$action_name]);
  }

  function getAccessibleActions($path, $service_name)
  {
    $service =& new Service($service_name);

    $actions = $service->getActionsList();

    $accessible_actions = array();

    $access = $this->_determineWhatAccessApply($path);

    foreach($actions as $action)
    {
      $action_propery = $service->getActionProperties($action);

      if(!isset($action_propery['access']))
        $accessible_actions[$action] = $action_propery;
      elseif ($action_propery['access'] <= $access)
        $accessible_actions[$action] = $action_propery;
    }

    return $accessible_actions;
  }

  function _determineWhatAccessApply($path)
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $groups = $user->get('groups');

    $result = 0;
    if (!count($groups))
      return $result;

    foreach($groups as $group)
    {
      $current_group_access = $this->_getAccessAppliedToGroup($path, $group);
      if ($current_group_access > $result)
        $result = $current_group_access;
    }

    return $result;
  }

  function _getAccessAppliedToGroup($object_path, $group)
  {
    $prev_matching = 0;
    $result = 0;

    foreach($this->policy as $policy_record)
    {
      if(($policy_record[1] == $group) &&
         $this->_isMoreAccurateMatching($policy_record[0], $object_path, $prev_matching))
      {
        $result = $policy_record[2];
      }
    }

    return $result;
  }

  function _isMoreAccurateMatching($path, $object_path, &$prev_matching)
  {
    $mathing = strlen(substr($path, $object_path));
    if ($mathing >= $prev_matching)
    {
      $prev_matching = $mathing;
      return true;
    }
    else
      return false;
  }

}
?>