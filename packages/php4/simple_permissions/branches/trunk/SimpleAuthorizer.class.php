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
require_once(LIMB_DIR . '/class/core/permissions/Authorizer.interface.php');

class SimpleAuthorizer implements Authorizer
{
  var $_cached_accessible_actions_properties = array();
  var $_cached_behaviour_actions = array();

  function getAccessibleObjectIds($object_ids, $action = 'display')
  {
    if (!count($object_ids))
      return array();

    $in_ids = implode(',', $object_ids);

    if(!$accessor_ids = $this->getUserAccessorIds())
      return array();

    $accessor_ids = implode(',', $accessor_ids);

    $toolkit =& Limb :: toolkit();
    $db =& $toolkit->getDB();

    $sql = "SELECT soa.object_id as id
      FROM sys_object_access as soa
      WHERE soa.object_id IN ({$in_ids})";

    $sql	.= " AND soa.accessor_id IN ({$accessor_ids})";

    $sql .=" AND soa.access = 1";

    $db->sqlExec($sql);

    return array_keys($db->getArray('id'));
  }

  function getUserAccessorIds()
  {
    $accessor_ids = array();

    $toolkit =& Limb :: toolkit();
    $user =& $toolkit->getUser();

    if(($user_id = $user->getId()) != DEFAULT_USER_ID)
      $accessor_ids[] = $user_id;

    $groups = $user->get('groups', array());
    foreach(array_keys($groups) as $group_id)
      $accessor_ids[] = $group_id;

    return $accessor_ids;
  }

  function assignActionsToObjects(&$objects_data)
  {
    if(isset($objects_data['id']))//hack which allows to accept objects arrays and single objects
      $arr[] =& $objects_data;
    else
      $arr =& $objects_data;

    $controllers = array();
    $actions = array();
    $accessible_actions = array();

    foreach($arr as $key => $data)
    {
      $behaviour_name = $data['behaviour'];
      $behaviour_id = $data['behaviour_id'];

      $arr[$key]['actions'] = $this->_getAccessibleActionsProperties($behaviour_id, $behaviour_name);
    }
  }

  function _getAccessibleActionsProperties($behaviour_id, $behaviour_name)
  {
    if(isset($this->_cached_accessible_actions_properties[$behaviour_id]))
      return $this->_cached_accessible_actions_properties[$behaviour_id];

    $actions = $this->_getBehaviourActions($behaviour_id, $behaviour_name);

    $behaviour_accessible_actions = $this->getBehaviourAccessibleActions($behaviour_id);
    $behaviour = $this->_getBehaviour($behaviour_name);

    $result = array();
    foreach($behaviour_accessible_actions as $action)
    {
      if (in_array($action, $actions))
      {
        $method = 'get' . ucfirst($action) . 'ActionProperties';
        $result[$action] = $behaviour->$method();
      }
    }

    $this->_cached_accessible_actions_properties[$behaviour_id] = $result;

    return $result;
  }

  function _getBehaviourActions($behaviour_id, $behaviour_name)
  {
    if(isset($this->_cached_behaviour_actions[$behaviour_id]))
      return $this->_cached_behaviour_actions[$behaviour_id];

    $behaviour = $this->_getBehaviour($behaviour_name);
    $this->_cached_behaviour_actions[$behaviour_id] = $behaviour->getActionsList();

    return $this->_cached_behaviour_actions[$behaviour_id];
  }

  //for mocking
  function &_getBehaviour($behaviour_name)
  {
    $toolkit =& Limb :: toolkit();
    return $toolkit->getBehaviour($behaviour_name);
  }

  function getBehaviourAccessibleActions($behaviour_id)
  {
    if(!$accessor_ids = $this->getUserAccessorIds())
      return array();

    $in_ids = implode(',', $accessor_ids);

    $toolkit =& Limb :: toolkit();
    $db =& $toolkit->getDB();

    $sql = "SELECT saa.action_name as action_name FROM sys_action_access as saa
      WHERE saa.behaviour_id = {$behaviour_id} AND
      saa.accessor_id IN ({$in_ids})
      GROUP BY saa.action_name";

    $db->sqlExec($sql);
    return array_keys($db->getArray('action_name'));
  }
}
?>