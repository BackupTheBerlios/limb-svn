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
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/class/lib/util/ComplexArray.class.php');

class AccessPolicy
{
  const ACCESSOR_TYPE_GROUP = 0;
  const ACCESSOR_TYPE_USER = 1;

  public function getObjectsAccessByIds($ids, $accessor_type)
  {
    if (!is_array($ids) ||  !count($ids))
      return array();

    $db_table = Limb :: toolkit()->createDBTable('SysObjectAccess');

    $ids_sql = 'object_id IN ('. implode(',', $ids) . ') AND accessor_type=' . $accessor_type;

    $arr = $db_table->getList($ids_sql);

    $result = array();
    foreach($arr as $id => $data)
      $result[$data['object_id']][$data['accessor_id']] = (int)$data['access'];

    return $result;
  }

  public function getActionsAccess($behaviour_id, $accessor_type)
  {
    $db_table = Limb :: toolkit()->createDBTable('SysActionAccess');

    $condition = 'behaviour_id ='. $behaviour_id . ' AND accessor_type=' . $accessor_type;

    $arr = $db_table->getList($condition);

    $result = array();
    foreach($arr as $id => $data)
      $result[$data['accessor_id']][$data['action_name']] = 1;

    return $result;
  }

  public function saveActionsAccess($behaviour_id, $policy_array, $accessor_type)
  {
    $db_table = Limb :: toolkit()->createDBTable('SysActionAccess');
    $conditions['behaviour_id'] = $behaviour_id;
    $conditions['accessor_type'] = $accessor_type;

    $db_table->delete($conditions);

    foreach($policy_array as $accessor_id => $access_data)
    {
      foreach($access_data as $action_name => $is_accessible)
      {
        if (!$is_accessible)
          continue;

        $data = array();
        $data['accessor_id'] = $accessor_id;
        $data['behaviour_id'] = $behaviour_id;
        $data['action_name'] = $action_name;
        $data['accessor_type'] = $accessor_type;

        $db_table->insert($data);
      }
    }

    return true;
  }

  public function saveNewObjectAccess($object, $parent_object, $action)
  {
    $behaviour_id = $parent_object->getBehaviourId();
    $object_id = $object->getId();
    $parent_object_id = $parent_object->getId();

    $group_template = $this->getAccessTemplate($behaviour_id, $action, self :: ACCESSOR_TYPE_GROUP);
    $user_template = $this->getAccessTemplate($behaviour_id, $action, self :: ACCESSOR_TYPE_USER);

    if (!count($group_template))
      $group_result = $this->copyObjectsAccess($object_id, $parent_object_id, self :: ACCESSOR_TYPE_GROUP);
    else
      $group_result = $this->saveObjectsAccess(array($object_id => $group_template), self :: ACCESSOR_TYPE_GROUP);

    if (!count($user_template))
      $user_result = $this->copyObjectsAccess($object_id, $parent_object_id, self :: ACCESSOR_TYPE_USER);
    else
      $user_result = $this->saveObjectsAccess(array($object_id => $user_template), self :: ACCESSOR_TYPE_USER);

    if (!$group_result &&  !$user_result)
    {
       throw new LimbException('parent object has no acccess records at all',
        array('parent_id' => $parent_object_id)
      );
    }
    else
      return true;
  }

  public function applyAccessTemplates($object, $action)
  {
    $behaviour_id = $object->getBehaviourId();
    $object_id = $object->getId();

    $user_templates = $this->getAccessTemplates($behaviour_id, self :: ACCESSOR_TYPE_USER);
    $group_templates = $this->getAccessTemplates($behaviour_id, self :: ACCESSOR_TYPE_GROUP);

    if(!isset($user_templates[$action]) &&  !isset($group_templates[$action]))
    {
       throw new LimbException('access template is not set',
        array('action' => $action, 'class_name' => get_class($object))
      );
    }

    $db_table = Limb :: toolkit()->createDBTable('SysObjectAccess');

    $conditions['object_id'] = $object_id;

    $db_table->delete($conditions);

    $this->saveObjectsAccess(array($object_id => $group_templates[$action]), self :: ACCESSOR_TYPE_GROUP);
    $this->saveObjectsAccess(array($object_id => $user_templates[$action]), self :: ACCESSOR_TYPE_USER);

    return true;
  }

  public function saveObjectsAccess($policy_array, $accessor_type, $accessor_ids = array())
  {
    $db_table = Limb :: toolkit()->createDBTable('SysObjectAccess');

    foreach($policy_array as $object_id => $access_data)
    {
      $conditions = 'object_id='. (int)$object_id . ' AND accessor_type=' . $accessor_type;
      if (count($accessor_ids))
        $conditions .= ' AND '. sqlIn('accessor_id', $accessor_ids);

      $db_table->delete($conditions);

      foreach($access_data as $accessor_id => $access)
      {
        if (!$access)
          continue;

        $data = array();
        $data['access'] = 1;
        $data['accessor_id'] = $accessor_id;
        $data['object_id'] = $object_id;
        $data['accessor_type'] = $accessor_type;

        $db_table->insert($data);
      }
    }

    return true;
  }

  public function copyObjectAccess($object_id, $source_id, $accessor_type)
  {
    $db_table = Limb :: toolkit()->createDBTable('SysObjectAccess');

    $conditions['object_id'] = $object_id;
    $conditions['accessor_type'] = $accessor_type;

    $db_table->delete($conditions);

    $conditions['object_id'] = $source_id;

    $rows = $db_table->getList($conditions);
    if(!count($rows))
      return false;

    foreach($rows as $id => $data)
    {
      $data['id'] = null;
      $data['object_id'] = $object_id;
      $db_table->insert($data);
    }

    return true;
  }

  public function saveAccessTemplates($behaviour_id, $template_array, $accessor_type)
  {
    $db_table = Limb :: toolkit()->createDBTable('SysActionAccessTemplate');
    $item_db_table= Limb :: toolkit()->createDBTable('SysActionAccessTemplateItem');

    $conditions['behaviour_id'] = $behaviour_id;
    $conditions['accessor_type'] = $accessor_type;
    $db_table->delete($conditions);

    foreach($template_array as $action_name => $access_data)
    {
      $data = array();

      $data['behaviour_id'] = $behaviour_id;
      $data['action_name'] = $action_name;
      $data['accessor_type'] = $accessor_type;
      $db_table->insert($data);
      $template_id = $db_table->getLastInsertId();

      foreach($access_data as $accessor_id => $access)
      {
        if (!$access)
          continue;
        $data = array();
        $data['accessor_id'] = $accessor_id;
        $data['template_id'] = $template_id;
        $data['access'] = 1;

        $item_db_table->insert($data);
      }
    }

    return true;
  }

  public function getAccessTemplates($behaviour_id, $accessor_type)
  {
    $db = Limb :: toolkit()->getDB();

    $sql = "SELECT
            saat.action_name as action_name,
            saat.behaviour_id as behaviour_id,
            saati.template_id as template_id,
            saati.accessor_id as accessor_id,
            saati.access as access
            FROM sys_action_access_template as saat,
            sys_action_access_template_item as saati
            WHERE saat.behaviour_id = {$behaviour_id} AND
            saati.template_id = saat.id AND saat.accessor_type = " . $accessor_type;

    $db->sqlExec($sql);
    $all_template_records = $db->getArray();

    if (!count($all_template_records))
      return array();

    $result = array();
    foreach($all_template_records as $data)
      $result[$data['action_name']][$data['accessor_id']] = $data['access'];

    return $result;
  }

  function saveObjectAccessForAction($object, $action)
  {
  }
}
?>