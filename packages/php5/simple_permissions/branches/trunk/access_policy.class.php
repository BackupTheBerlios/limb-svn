<?php
/**********************************************************************************
* Copyright 2004 BIT, Ltd. http://limb-project.com, mailto: limb@0x00.ru
*
* Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
***********************************************************************************
*
* $Id$
*
***********************************************************************************/
require_once(LIMB_DIR . '/class/lib/system/objects_support.inc.php');
require_once(LIMB_DIR . '/class/db_tables/db_table_factory.class.php');
require_once(LIMB_DIR . '/class/lib/util/complex_array.class.php');
require_once(LIMB_DIR . '/class/core/permissions/user.class.php');

class access_policy
{
  const ACCESSOR_TYPE_GROUP = 0;
  const ACCESSOR_TYPE_USER = 1;

  public function get_objects_access_by_ids($ids, $accessor_type)
  {
    if (!is_array($ids) || !count($ids))
      return array();

    $db_table = Limb :: toolkit()->createDBTable('sys_object_access');

    $ids_sql = 'object_id IN ('. implode(',', $ids) . ') AND accessor_type=' . $accessor_type;

    $arr = $db_table->get_list($ids_sql);

    $result = array();
    foreach($arr as $id => $data)
      $result[$data['object_id']][$data['accessor_id']] = (int)$data['access'];

    return $result;
  }

  public function get_actions_access($class_id, $accessor_type)
  {
    $db_table = Limb :: toolkit()->createDBTable('sys_action_access');

    $condition = 'class_id ='. $class_id . ' AND accessor_type=' . $accessor_type;

    $arr = $db_table->get_list($condition);

    $result = array();
    foreach($arr as $id => $data)
      $result[$data['accessor_id']][$data['action_name']] = 1;

    return $result;
  }

  public function save_actions_access($class_id, $policy_array, $accessor_type)
  {
    $db_table = Limb :: toolkit()->createDBTable('sys_action_access');
    $conditions['class_id'] = $class_id;
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
        $data['class_id'] = $class_id;
        $data['action_name'] = $action_name;
        $data['accessor_type'] = $accessor_type;

        $db_table->insert($data);
      }
    }

    return true;
  }

  public function save_new_object_access($object, $parent_object, $action)
  {
    $class_id = $parent_object->get_class_id();
    $object_id = $object->get_id();
    $parent_object_id = $parent_object->get_id();

    $group_template = $this->get_access_template($class_id, $action, self :: ACCESSOR_TYPE_GROUP);
    $user_template = $this->get_access_template($class_id, $action, self :: ACCESSOR_TYPE_USER);

    if (!count($group_template))
      $group_result = $this->copy_objects_access($object_id, $parent_object_id, self :: ACCESSOR_TYPE_GROUP);
    else
      $group_result = $this->save_objects_access(array($object_id => $group_template), self :: ACCESSOR_TYPE_GROUP);

    if (!count($user_template))
      $user_result = $this->copy_objects_access($object_id, $parent_object_id, self :: ACCESSOR_TYPE_USER);
    else
      $user_result = $this->save_objects_access(array($object_id => $user_template), self :: ACCESSOR_TYPE_USER);

    if (!$group_result && !$user_result)
    {
       throw new LimbException('parent object has no acccess records at all',
        array('parent_id' => $parent_object_id)
      );
    }
    else
      return true;
  }

  public function apply_access_templates($object, $action)
  {
    $class_id = $object->get_class_id();
    $object_id = $object->get_id();

    $user_templates = $this->get_access_templates($class_id, self :: ACCESSOR_TYPE_USER);
    $group_templates = $this->get_access_templates($class_id, self :: ACCESSOR_TYPE_GROUP);

    if(!isset($user_templates[$action]) && !isset($group_templates[$action]))
    {
       throw new LimbException('access template is not set',
        array('action' => $action, 'class_name' => get_class($object))
      );
    }

    $db_table = Limb :: toolkit()->createDBTable('sys_object_access');

    $conditions['object_id'] = $object_id;

    $db_table->delete($conditions);

    $this->save_objects_access(array($object_id => $group_templates[$action]), self :: ACCESSOR_TYPE_GROUP);
    $this->save_objects_access(array($object_id => $user_templates[$action]), self :: ACCESSOR_TYPE_USER);

    return true;
  }

  public function save_objects_access($policy_array, $accessor_type, $accessor_ids = array())
  {
    $db_table = Limb :: toolkit()->createDBTable('sys_object_access');

    foreach($policy_array as $object_id => $access_data)
    {
      $conditions = 'object_id='. (int)$object_id . ' AND accessor_type=' . $accessor_type;
      if (count($accessor_ids))
        $conditions .= ' AND '. sql_in('accessor_id', $accessor_ids);

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

  public function copy_object_access($object_id, $source_id, $accessor_type)
  {
    $db_table = Limb :: toolkit()->createDBTable('sys_object_access');

    $conditions['object_id'] = $object_id;
    $conditions['accessor_type'] = $accessor_type;

    $db_table->delete($conditions);

    $conditions['object_id'] = $source_id;

    $rows = $db_table->get_list($conditions);
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

  public function save_access_templates($class_id, $template_array, $accessor_type)
  {
    $db_table = Limb :: toolkit()->createDBTable('sys_action_access_template');
    $item_db_table= Limb :: toolkit()->createDBTable('sys_action_access_template_item');

    $conditions['class_id'] = $class_id;
    $conditions['accessor_type'] = $accessor_type;
    $db_table->delete($conditions);

    foreach($template_array as $action_name => $access_data)
    {
      $data = array();

      $data['class_id'] = $class_id;
      $data['action_name'] = $action_name;
      $data['accessor_type'] = $accessor_type;
      $db_table->insert($data);
      $template_id = $db_table->get_last_insert_id();

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

  public function get_access_templates($class_id, $accessor_type)
  {
    $db = Limb :: toolkit()->getDB();

    $sql = "SELECT
            saat.action_name as action_name,
            saat.class_id as class_id,
            saati.template_id as template_id,
            saati.accessor_id as accessor_id,
            saati.access as access
            FROM sys_action_access_template as saat,
            sys_action_access_template_item as saati
            WHERE saat.class_id = {$class_id} AND
            saati.template_id = saat.id AND saat.accessor_type = " . $accessor_type;

    $db->sql_exec($sql);
    $all_template_records = $db->get_array();

    if (!count($all_template_records))
      return array();

    $result = array();
    foreach($all_template_records as $data)
      $result[$data['action_name']][$data['accessor_id']] = $data['access'];

    return $result;
  }
  
  function save_object_access_for_action($object, $action)
  {
  }
}
?>