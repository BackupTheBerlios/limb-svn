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

class SimpleACLAuthorizer// implements Authorizer
{
  var $policy = array();

  function attachPolicy($path, $group_name, $access)
  {
    $this->policy[] = array($path, $group_name, $access);
  }

  function canDo($action_name, &$service)
  {
    $this->assignActions(&$service);

    $actions = $service->get('actions');

    return isset($actions[$action_name]);
  }

  function assignActions(&$service)
  {
    if($actions = $service->get('actions'))
      return;

    $behaviour =& $service->getBehaviour();
    $actions = $behaviour->getActionsList();

    $path = $service->get('path');
    $accessible_actions = array();

    $access = $this->_determineWhatAccessApplyToService(&$service);

    foreach($actions as $action)
    {
      $method = 'get' . ucfirst($action) . 'ActionProperties';
      $action_propery = $behaviour->$method();

      if ($action_propery['access'] <= $access)
        $accessible_actions[$action] = $action_propery;
    }

    $service->set('actions', $accessible_actions);
  }

  function _determineWhatAccessApplyToService(&$service)
  {
    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();
    $groups = $user->get('groups');

    $result = 0;
    if (!count($groups))
      return $result;

    $path = $service->get('path');
    foreach($groups as $group)
    {
      $current_group_access = $this->_getAccessAppliedToGroup($path, $group);
      if ($current_group_access > $result)
        $result = $current_group_access;
    }

    return $result;
  }

  function _getAccessAppliedToGroup($service_path, $group)
  {
    $prev_matching = 0;
    $result = 0;

    foreach($this->policy as $policy_record)
    {
      if(($policy_record[1] == $group) &&
         $this->_isMoreAccurateMatching($policy_record[0], $service_path, &$prev_matching))
      {
        $result = $policy_record[2];
      }
    }

    return $result;
  }

  function _isMoreAccurateMatching($path, $service_path, &$prev_matching)
  {
    $mathing = strlen(substr($path, $service_path));
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