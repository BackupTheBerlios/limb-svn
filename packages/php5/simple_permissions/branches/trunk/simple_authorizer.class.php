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
require_once(LIMB_DIR . '/class/core/permissions/authorizer.interface.php');

class simple_authorizer implements authorizer
{
  protected $_cached_accessible_actions_properties = array();
  protected $_cached_behaviour_actions = array();

  public function get_accessible_object_ids($object_ids, $action = 'display')
  {
    if (!count($object_ids))
      return array();

    $in_ids = implode(',', $object_ids);

    if(!$accessor_ids = $this->get_user_accessor_ids())
      return array();

    $accessor_ids = implode(',', $accessor_ids);

    $db = Limb :: toolkit()->getDB();

    $sql = "SELECT soa.object_id as id
      FROM sys_object_access as soa
      WHERE soa.object_id IN ({$in_ids})";

    $sql	.= " AND soa.accessor_id IN ({$accessor_ids})";

    $sql .=" AND soa.access = 1";

    $db->sql_exec($sql);

    return array_keys($db->get_array('id'));
  }

  public function get_user_accessor_ids()
  {
    $accessor_ids = array();

    $user = Limb :: toolkit()->getUser();

    if(($user_id = $user->get_id()) != user :: DEFAULT_USER_ID)
      $accessor_ids[] = $user_id;

    $groups = $user->get('groups', array());
    foreach(array_keys($groups) as $group_id)
      $accessor_ids[] = $group_id;

    return $accessor_ids;
  }

  public function assign_actions_to_objects(&$objects_data)
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

      $arr[$key]['actions'] = $this->_get_accessible_actions_properties($behaviour_id, $behaviour_name);
    }
  }

  protected function _get_accessible_actions_properties($behaviour_id, $behaviour_name)
  {
    if(isset($this->_cached_accessible_actions_properties[$behaviour_id]))
      return $this->_cached_accessible_actions_properties[$behaviour_id];

    $actions = $this->_get_behaviour_actions($behaviour_id, $behaviour_name);

    $behaviour_accessible_actions = $this->get_behaviour_accessible_actions($behaviour_id);
    $behaviour = $this->_get_behaviour($behaviour_name);

    $result = array();
    foreach($behaviour_accessible_actions as $action)
    {
      if (in_array($action, $actions))
      {
        $method = 'get_' . $action . '_action_properties';
        $result[$action] = $behaviour->$method();
      }
    }

    $this->_cached_accessible_actions_properties[$behaviour_id] = $result;

    return $result;
  }

  protected function _get_behaviour_actions($behaviour_id, $behaviour_name)
  {
    if(isset($this->_cached_behaviour_actions[$behaviour_id]))
      return $this->_cached_behaviour_actions[$behaviour_id];

    $behaviour = $this->_get_behaviour($behaviour_name);
    $this->_cached_behaviour_actions[$behaviour_id] = $behaviour->get_actions_list();

    return $this->_cached_behaviour_actions[$behaviour_id];
  }

  //for mocking
  protected function _get_behaviour($behaviour_name)
  {
    return Limb :: toolkit()->getBehaviour($behaviour_name);
  }

  public function get_behaviour_accessible_actions($behaviour_id)
  {
    if(!$accessor_ids = $this->get_user_accessor_ids())
      return array();

    $in_ids = implode(',', $accessor_ids);

    $db = Limb :: toolkit()->getDB();

    $sql = "SELECT saa.action_name as action_name FROM sys_action_access as saa
      WHERE saa.behaviour_id = {$behaviour_id} AND
      saa.accessor_id IN ({$in_ids})
      GROUP BY saa.action_name";

    $db->sql_exec($sql);
    return array_keys($db->get_array('action_name'));
  }
}
?>